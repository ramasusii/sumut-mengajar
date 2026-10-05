<?php
namespace app\controllers;

use app\components\PhoneHelper;
use app\models\Application;
use app\models\ApplicationAnswer;
use app\models\ApplicationDocument;
use app\models\ApplicantProfile;
use app\models\RecruitmentBatch;
use app\models\RecruitmentFormField;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class ApplicantController extends Controller
{
    public $layout = 'applicant';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['applicant']],
                ],
                'denyCallback' => function () {
                    if (Yii::$app->user->isGuest) {
                        Yii::$app->user->setReturnUrl(Yii::$app->request->url);
                        return Yii::$app->response->redirect(['/site/login']);
                    }
                    if (Yii::$app->user->can('superAdmin') || Yii::$app->user->can('adminGsm') || Yii::$app->user->can('reviewer')) {
                        return Yii::$app->response->redirect(['/admin-dashboard/index']);
                    }
                    throw new ForbiddenHttpException('Akun ini tidak memiliki akses ke Portal Peserta.');
                },
            ],
        ];
    }

    public function actionDashboard()
    {
        $applications = Application::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->with(['batch.kabupatenKota'])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $today = date('Y-m-d');
        $openBatches = RecruitmentBatch::find()
            ->where(['status' => RecruitmentBatch::STATUS_OPEN])
            ->andWhere(['<=', 'registration_start', $today])
            ->andWhere(['>=', 'registration_end', $today])
            ->with('kabupatenKota')
            ->orderBy(['registration_end' => SORT_ASC])
            ->all();

        return $this->render('dashboard', compact('applications', 'openBatches'));
    }

    public function actionApply($id)
    {
        $batch = RecruitmentBatch::findOne($id);
        if (!$batch) {
            throw new NotFoundHttpException('Batch tidak ditemukan.');
        }
        if (!$batch->isOpen()) {
            throw new ForbiddenHttpException('Pendaftaran batch ini sedang tidak dibuka.');
        }

        $app = Application::findOne(['batch_id' => $batch->id, 'user_id' => Yii::$app->user->id]);
        if (!$app) {
            $app = new Application([
                'batch_id' => $batch->id,
                'user_id' => Yii::$app->user->id,
                'status' => Application::STATUS_DRAFT,
            ]);
            do {
                $app->application_code = Application::generateCode($batch->id);
            } while (Application::find()->where(['application_code' => $app->application_code])->exists());

            if (!$app->save()) {
                Yii::$app->session->setFlash('error', 'Pendaftaran belum dapat dibuat.');
                return $this->redirect(['dashboard']);
            }
        }

        if ($app->status === Application::STATUS_DRAFT) {
            return $this->redirect(['/applicant/form', 'id' => $app->id, 'step' => 1]);
        }
        if ($app->status === Application::STATUS_REVISION_REQUIRED) {
            return $this->redirect(['/applicant/form', 'id' => $app->id, 'step' => 3]);
        }
        return $this->redirect(['application', 'id' => $app->id]);
    }

    public function actionApplication($id)
    {
        return $this->render('application', ['model' => $this->findOwnApplication($id)]);
    }

    public function actionForm($id, $step = 1)
    {
        $app = $this->findOwnApplication($id);
        $editableStatuses = [Application::STATUS_DRAFT, Application::STATUS_REVISION_REQUIRED];
        if (!in_array($app->status, $editableStatuses, true)) {
            return $this->redirect(['/applicant/application', 'id' => $app->id]);
        }

        if ($app->status === Application::STATUS_DRAFT && !$app->batch->isOpen()) {
            throw new ForbiddenHttpException('Pendaftaran batch ini sudah ditutup.');
        }

        $isRevision = $app->status === Application::STATUS_REVISION_REQUIRED;
        $step = max(1, min(4, (int)$step));
        if ($isRevision && $step < 3) {
            $step = 3;
        }

        $profile = ApplicantProfile::findOne(['user_id' => Yii::$app->user->id]);
        if (!$profile) {
            $profile = new ApplicantProfile([
                'user_id' => Yii::$app->user->id,
                'nama_lengkap' => Yii::$app->user->identity->nama ?: '',
                'nomor_whatsapp' => Yii::$app->user->identity->whatsapp ?: '',
            ]);
        }

        $fields = RecruitmentFormField::find()
            ->where(['batch_id' => $app->batch_id, 'is_active' => 1])
            ->with('options')
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])
            ->all();

        $answers = ApplicationAnswer::find()
            ->where(['application_id' => $app->id])
            ->indexBy('field_id')
            ->all();

        $documents = $this->groupDocuments($app->id);
        $errors = [];

        if (Yii::$app->request->isPost) {
            if ($step === 1 && !$isRevision) {
                $errors = $this->saveProfileStep($profile);
                if (!$errors) {
                    Yii::$app->session->setFlash('success', 'Data diri berhasil disimpan.');
                    return $this->redirect(['/applicant/form', 'id' => $app->id, 'step' => 2]);
                }
            }

            if ($step === 2 && !$isRevision) {
                $errors = $this->saveAnswerStep($app, $fields);
                if (!$errors) {
                    Yii::$app->session->setFlash('success', 'Jawaban berhasil disimpan.');
                    return $this->redirect(['/applicant/form', 'id' => $app->id, 'step' => 3]);
                }
                $answers = ApplicationAnswer::find()->where(['application_id' => $app->id])->indexBy('field_id')->all();
            }

            if ($step === 3) {
                $errors = $this->saveDocumentStep($app, $fields, $documents);
                if (!$errors) {
                    Yii::$app->session->setFlash('success', $isRevision ? 'Perbaikan dokumen berhasil disimpan.' : 'Dokumen berhasil disimpan.');
                    return $this->redirect(['/applicant/form', 'id' => $app->id, 'step' => 4]);
                }
                $documents = $this->groupDocuments($app->id);
            }
        }

        return $this->render('form', [
            'application' => $app,
            'profile' => $profile,
            'fields' => $fields,
            'answers' => $answers,
            'documents' => $documents,
            'step' => $step,
            'errors' => $errors,
            'isRevision' => $isRevision,
        ]);
    }

    public function actionSubmit($id)
    {
        $app = $this->findOwnApplication($id);
        if (!in_array($app->status, [Application::STATUS_DRAFT, Application::STATUS_REVISION_REQUIRED], true)) {
            return $this->redirect(['/applicant/application', 'id' => $app->id]);
        }
        if (!Yii::$app->request->isPost) {
            return $this->redirect(['/applicant/form', 'id' => $app->id, 'step' => 4]);
        }

        $wasRevision = $app->status === Application::STATUS_REVISION_REQUIRED;
        $errors = $this->validateReadyToSubmit($app);
        if ($errors) {
            Yii::$app->session->setFlash('error', 'Pendaftaran belum dapat dikirim: ' . implode(' ', $errors));
            return $this->redirect(['/applicant/form', 'id' => $app->id, 'step' => 4]);
        }

        $app->status = Application::STATUS_SUBMITTED;
        $app->submitted_at = date('Y-m-d H:i:s');
        $app->verified_by = null;
        $app->verified_at = null;
        $app->save(false, ['status', 'submitted_at', 'verified_by', 'verified_at', 'updated_at']);

        try {
            Yii::$app->db->createCommand()->insert('application_stage_history', [
                'application_id' => $app->id,
                'stage_code' => $wasRevision ? 'resubmitted' : 'submitted',
                'status' => 'completed',
                'note' => $wasRevision ? 'Perbaikan berkas dikirim ulang oleh peserta.' : 'Pendaftaran dikirim oleh peserta.',
                'changed_by' => Yii::$app->user->id,
                'created_at' => time(),
            ])->execute();
        } catch (\Throwable $e) {
            Yii::warning($e, __METHOD__);
        }

        Yii::$app->session->setFlash('success', $wasRevision
            ? 'Perbaikan berhasil dikirim. Berkas akan diperiksa kembali oleh panitia.'
            : 'Pendaftaran berhasil dikirim. Pantau proses verifikasi melalui halaman ini.');

        return $this->redirect(['/applicant/application', 'id' => $app->id]);
    }

    private function findOwnApplication($id): Application
    {
        $app = Application::find()
            ->where(['id' => $id, 'user_id' => Yii::$app->user->id])
            ->with(['batch.kabupatenKota'])
            ->one();
        if (!$app) {
            throw new NotFoundHttpException('Pendaftaran tidak ditemukan.');
        }
        return $app;
    }

    private function saveProfileStep(ApplicantProfile $profile): array
    {
        $data = Yii::$app->request->post('profile', []);
        $attributes = [
            'nama_lengkap', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama',
            'alamat_domisili', 'provinsi_domisili', 'kabupaten_kota_domisili',
            'nomor_whatsapp', 'asal_instansi', 'pendidikan_terakhir', 'pekerjaan',
            'instagram', 'tiktok',
        ];

        foreach ($attributes as $attribute) {
            if ($profile->hasAttribute($attribute)) {
                $profile->$attribute = isset($data[$attribute]) ? trim((string)$data[$attribute]) : null;
            }
        }

        $profile->nomor_whatsapp = PhoneHelper::normalizeIndonesia($profile->nomor_whatsapp) ?: $profile->nomor_whatsapp;
        $profile->user_id = Yii::$app->user->id;
        $errors = $this->validateProfileEligibility($profile);
        if ($errors) {
            return $errors;
        }

        if ($profile->hasAttribute('profile_completed_at')) {
            $profile->profile_completed_at = date('Y-m-d H:i:s');
        }
        if (!$profile->save(false)) {
            return ['Data diri belum dapat disimpan.'];
        }

        $user = Yii::$app->user->identity;
        $changed = false;
        if ($profile->nama_lengkap && $user->nama !== $profile->nama_lengkap) {
            $user->nama = $profile->nama_lengkap;
            $changed = true;
        }
        if ($profile->nomor_whatsapp && !$user->whatsapp) {
            $user->whatsapp = $profile->nomor_whatsapp;
            $changed = true;
        }
        if ($changed) {
            $user->save(false);
        }

        return [];
    }

    private function saveAnswerStep(Application $app, array $fields): array
    {
        $postedAnswers = Yii::$app->request->post('answers', []);
        $errors = [];

        foreach ($fields as $field) {
            if ($field->isFileField()) {
                continue;
            }

            $value = $postedAnswers[$field->id] ?? null;
            if (is_array($value)) {
                $value = array_values(array_filter(array_map(static fn($v) => trim((string)$v), $value), static fn($v) => $v !== ''));
                $isEmpty = !$value;
            } else {
                $value = is_string($value) ? trim($value) : $value;
                $isEmpty = ($value === null || $value === '');
            }

            $answer = ApplicationAnswer::findOne(['application_id' => $app->id, 'field_id' => $field->id]);

            if ((int)$field->is_required === 1 && $isEmpty) {
                $errors[] = $field->label . ' wajib diisi.';
                continue;
            }

            if ($isEmpty) {
                if ($answer) {
                    $answer->delete();
                }
                continue;
            }

            if (!$answer) {
                $answer = new ApplicationAnswer(['application_id' => $app->id, 'field_id' => $field->id]);
            }

            if (is_array($value)) {
                $answer->answer_text = null;
                $answer->answer_json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                $answer->answer_text = (string)$value;
                $answer->answer_json = null;
            }
            $answer->save(false);
        }

        return $errors;
    }

    private function saveDocumentStep(Application $app, array $fields, array $existingByField): array
    {
        $errors = [];
        $uploadRoot = Yii::getAlias('@app/storage/private/applications/' . $app->id);
        FileHelper::createDirectory($uploadRoot, 0775, true);

        foreach ($fields as $field) {
            if (!$field->isFileField()) {
                continue;
            }

            $existing = $existingByField[$field->id] ?? [];
            $existingUsable = array_values(array_filter($existing, static fn($doc) => $doc->verification_status !== 'invalid'));
            [$minFiles, $maxFiles] = $this->fieldFileLimits($field);

            $uploads = $field->field_type === 'multi_file'
                ? UploadedFile::getInstancesByName('upload_' . $field->id)
                : array_filter([UploadedFile::getInstanceByName('upload_' . $field->id)]);

            if ($field->field_type === 'file' && $uploads) {
                foreach ($existing as $old) {
                    $this->deleteStoredDocument($old);
                    $old->delete();
                }
                $existingUsable = [];
            }

            if ($field->field_type === 'multi_file' && $uploads) {
                foreach ($existing as $old) {
                    if ($old->verification_status === 'invalid') {
                        $this->deleteStoredDocument($old);
                        $old->delete();
                    }
                }
                $existingUsable = array_values(array_filter($existingUsable, static fn($doc) => $doc->verification_status !== 'invalid'));
            }

            if (count($existingUsable) + count($uploads) > $maxFiles) {
                $errors[] = $field->label . ' maksimal ' . $maxFiles . ' file.';
                continue;
            }

            foreach ($uploads as $uploaded) {
                try {
                    $this->storeDocument($app, $field, $uploaded, $uploadRoot);
                } catch (\Throwable $e) {
                    $errors[] = $field->label . ': ' . $e->getMessage();
                }
            }

            $usableCount = ApplicationDocument::find()
                ->where(['application_id' => $app->id, 'field_id' => $field->id])
                ->andWhere(['<>', 'verification_status', 'invalid'])
                ->count();

            if ((int)$field->is_required === 1 && (int)$usableCount < $minFiles) {
                $errors[] = $field->label . ' membutuhkan minimal ' . $minFiles . ' file.';
            }
        }

        return array_values(array_unique($errors));
    }

    private function storeDocument(Application $app, RecruitmentFormField $field, UploadedFile $uploaded, string $uploadRoot): void
    {
        if ($uploaded->size <= 0 || $uploaded->size > 10 * 1024 * 1024) {
            throw new \RuntimeException('ukuran file maksimal 10 MB.');
        }

        $mime = FileHelper::getMimeType($uploaded->tempName);
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/pdf' => 'pdf',
        ];
        if (!isset($allowed[$mime])) {
            throw new \RuntimeException('format file harus JPG, PNG, atau PDF.');
        }

        $name = 'field-' . $field->id . '-' . time() . '-' . Yii::$app->security->generateRandomString(8) . '.' . $allowed[$mime];
        $absolutePath = $uploadRoot . '/' . $name;
        if (!$uploaded->saveAs($absolutePath)) {
            throw new \RuntimeException('file belum berhasil disimpan.');
        }

        $document = new ApplicationDocument([
            'application_id' => $app->id,
            'field_id' => $field->id,
            'document_type' => $field->field_key,
            'file_path' => 'private/applications/' . $app->id . '/' . $name,
            'original_name' => $uploaded->name,
            'mime_type' => $mime,
            'file_size' => $uploaded->size,
            'verification_status' => 'pending',
            'created_at' => time(),
        ]);
        $document->save(false);
    }

    private function deleteStoredDocument(ApplicationDocument $document): void
    {
        $absolute = $document->getAbsolutePath();
        if ($absolute && is_file($absolute)) {
            @unlink($absolute);
        }
    }

    private function groupDocuments(int $applicationId): array
    {
        $rows = ApplicationDocument::find()->where(['application_id' => $applicationId])->orderBy(['id' => SORT_ASC])->all();
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row->field_id][] = $row;
        }
        return $grouped;
    }

    private function fieldFileLimits(RecruitmentFormField $field): array
    {
        $config = json_decode((string)$field->validation_json, true);
        $config = is_array($config) ? $config : [];
        $min = max(1, (int)($config['min_files'] ?? 1));
        $max = $field->field_type === 'multi_file' ? max($min, (int)($config['max_files'] ?? 5)) : 1;
        return [$min, $max];
    }

    private function validateReadyToSubmit(Application $app): array
    {
        $errors = [];
        $profile = ApplicantProfile::findOne(['user_id' => $app->user_id]);
        if (!$profile) {
            $errors[] = 'Data diri belum lengkap.';
        } else {
            $errors = array_merge($errors, $this->validateProfileEligibility($profile));
        }

        $fields = RecruitmentFormField::find()->where([
            'batch_id' => $app->batch_id,
            'is_active' => 1,
            'is_required' => 1,
        ])->all();

        foreach ($fields as $field) {
            if ($field->isFileField()) {
                [$minFiles] = $this->fieldFileLimits($field);
                $count = ApplicationDocument::find()
                    ->where(['application_id' => $app->id, 'field_id' => $field->id])
                    ->andWhere(['<>', 'verification_status', 'invalid'])
                    ->count();
                if ((int)$count < $minFiles) {
                    $errors[] = 'Dokumen "' . $field->label . '" belum lengkap.';
                }
                continue;
            }

            $answer = ApplicationAnswer::findOne(['application_id' => $app->id, 'field_id' => $field->id]);
            if (!$answer || (trim((string)$answer->answer_text) === '' && trim((string)$answer->answer_json) === '')) {
                $errors[] = 'Pertanyaan "' . $field->label . '" belum dijawab.';
            }
        }

        return array_values(array_unique($errors));
    }

    private function validateProfileEligibility(ApplicantProfile $profile): array
    {
        $required = [
            'nama_lengkap' => 'Nama lengkap',
            'jenis_kelamin' => 'Jenis kelamin',
            'tempat_lahir' => 'Tempat lahir',
            'tanggal_lahir' => 'Tanggal lahir',
            'agama' => 'Agama',
            'alamat_domisili' => 'Alamat domisili',
            'provinsi_domisili' => 'Provinsi domisili',
            'kabupaten_kota_domisili' => 'Kabupaten/Kota domisili',
            'nomor_whatsapp' => 'Nomor WhatsApp',
            'asal_instansi' => 'Asal instansi',
            'pendidikan_terakhir' => 'Jenjang pendidikan',
        ];

        $errors = [];
        foreach ($required as $attribute => $label) {
            if (!$profile->hasAttribute($attribute) || trim((string)$profile->$attribute) === '') {
                $errors[] = $label . ' wajib diisi.';
            }
        }

        if ($profile->pendidikan_terakhir && !in_array($profile->pendidikan_terakhir, ['S1', 'D4'], true)) {
            $errors[] = 'Jenjang pendidikan yang dapat mendaftar adalah S1 atau D4.';
        }

        if (!PhoneHelper::normalizeIndonesia($profile->nomor_whatsapp)) {
            $errors[] = 'Nomor WhatsApp tidak valid.';
        }

        if ($profile->tanggal_lahir) {
            try {
                $timezone = new \DateTimeZone('Asia/Jakarta');
                $birthDate = new \DateTimeImmutable($profile->tanggal_lahir, $timezone);
                $today = new \DateTimeImmutable('today', $timezone);
                if ($birthDate > $today) {
                    $errors[] = 'Tanggal lahir tidak valid.';
                } else {
                    $age = $birthDate->diff($today)->y;
                    if ($age < 18 || $age > 28) {
                        $errors[] = 'Usia pendaftar harus 18 sampai 28 tahun pada saat pendaftaran.';
                    }
                }
            } catch (\Throwable $e) {
                $errors[] = 'Tanggal lahir tidak valid.';
            }
        }

        return array_values(array_unique($errors));
    }
}
