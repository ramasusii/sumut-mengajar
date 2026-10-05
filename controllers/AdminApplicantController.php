<?php
namespace app\controllers;

use app\models\Application;
use app\models\ApplicationAnswer;
use app\models\ApplicationDocument;
use app\models\ApplicationNote;
use app\models\RecruitmentBatch;
use app\models\RecruitmentFormField;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class AdminApplicantController extends Controller
{
    public $layout = 'main';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['superAdmin', 'adminGsm', 'reviewer']],
                ],
                'denyCallback' => function () {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['/site/staff-login']);
                    }
                    if (Yii::$app->user->can('applicant')) {
                        return Yii::$app->response->redirect(['/applicant/dashboard']);
                    }
                    throw new ForbiddenHttpException('Akun ini tidak memiliki akses ke data pendaftar.');
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'status' => ['post'],
                    'document-status' => ['post'],
                    'note' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $q = trim((string)Yii::$app->request->get('q', ''));
        $status = trim((string)Yii::$app->request->get('status', ''));
        $batchId = (int)Yii::$app->request->get('batch_id', 0);

        $query = Application::find()
            ->alias('a')
            ->joinWith(['user u'])
            ->joinWith(['profile p'])
            ->joinWith(['batch b'])
            ->with(['batch.kabupatenKota', 'documents']);

        if ($q !== '') {
            $query->andWhere([
                'or',
                ['like', 'a.application_code', $q],
                ['like', 'u.nama', $q],
                ['like', 'u.email', $q],
                ['like', 'u.whatsapp', $q],
                ['like', 'p.nama_lengkap', $q],
                ['like', 'p.nomor_whatsapp', $q],
            ]);
        }
        if ($status !== '') {
            $query->andWhere(['a.status' => $status]);
        }
        if ($batchId > 0) {
            $query->andWhere(['a.batch_id' => $batchId]);
        }

        $pagination = new Pagination([
            'totalCount' => (clone $query)->count('a.id'),
            'pageSize' => 20,
            'pageSizeParam' => false,
        ]);

        $models = $query
            ->orderBy([
                new \yii\db\Expression("CASE WHEN a.status = 'submitted' THEN 0 WHEN a.status = 'revision_required' THEN 1 WHEN a.status = 'draft' THEN 3 ELSE 2 END"),
                'a.submitted_at' => SORT_DESC,
                'a.id' => SORT_DESC,
            ])
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $batches = RecruitmentBatch::find()->orderBy(['batch_number' => SORT_DESC, 'id' => SORT_DESC])->all();
        $summary = [
            'total' => (int)Application::find()->count(),
            'submitted' => (int)Application::find()->where(['status' => Application::STATUS_SUBMITTED])->count(),
            'revision' => (int)Application::find()->where(['status' => Application::STATUS_REVISION_REQUIRED])->count(),
            'final' => (int)Application::find()->where(['status' => Application::STATUS_FINAL_PASS])->count(),
        ];

        return $this->render('index', compact('models', 'pagination', 'batches', 'summary', 'q', 'status', 'batchId'));
    }

    public function actionView($id)
    {
        $model = Application::find()
            ->where(['application.id' => $id])
            ->with(['user', 'profile.kabupatenKota', 'batch.kabupatenKota', 'verifier', 'notes.user'])
            ->one();
        if (!$model) {
            throw new NotFoundHttpException('Pendaftar tidak ditemukan.');
        }

        $fields = RecruitmentFormField::find()
            ->where(['batch_id' => $model->batch_id, 'is_active' => 1])
            ->with('options')
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])
            ->all();
        $answers = ApplicationAnswer::find()->where(['application_id' => $model->id])->indexBy('field_id')->all();
        $documents = $this->groupDocuments($model->id);

        $requiredDocumentCount = 0;
        $uploadedRequiredCount = 0;
        $validRequiredCount = 0;
        foreach ($fields as $field) {
            if (!$field->isFileField() || !(int)$field->is_required) {
                continue;
            }
            [$minFiles] = $this->fieldFileLimits($field);
            $requiredDocumentCount += $minFiles;
            $rows = $documents[$field->id] ?? [];
            $uploadedRequiredCount += count(array_filter($rows, static fn($doc) => $doc->verification_status !== 'invalid'));
            $validRequiredCount += count(array_filter($rows, static fn($doc) => $doc->verification_status === 'valid'));
        }

        return $this->render('view', compact('model', 'fields', 'answers', 'documents', 'requiredDocumentCount', 'uploadedRequiredCount', 'validRequiredCount'));
    }

    public function actionDocument($id, $documentId)
    {
        $document = ApplicationDocument::findOne(['id' => $documentId, 'application_id' => $id]);
        if (!$document) {
            throw new NotFoundHttpException('Dokumen tidak ditemukan.');
        }
        $path = $document->getAbsolutePath();
        if (!$path) {
            throw new NotFoundHttpException('File dokumen tidak ditemukan pada penyimpanan.');
        }

        return Yii::$app->response->sendFile(
            $path,
            $document->original_name ?: basename($path),
            ['inline' => true, 'mimeType' => $document->mime_type ?: null]
        );
    }

    public function actionStatus($id)
    {
        $model = Application::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Pendaftar tidak ditemukan.');
        }

        $status = (string)Yii::$app->request->post('status');
        $note = trim((string)Yii::$app->request->post('status_note', ''));
        $allowedNext = $this->allowedNextStatuses($model->status);

        if (!in_array($status, $allowedNext, true)) {
            Yii::$app->session->setFlash('error', 'Perubahan status tidak sesuai urutan tahapan seleksi.');
            return $this->redirect(['view', 'id' => $id]);
        }

        if (in_array($status, [Application::STATUS_REJECTED, Application::STATUS_REVISION_REQUIRED], true) && $note === '') {
            Yii::$app->session->setFlash('error', 'Catatan wajib diisi untuk keputusan ini.');
            return $this->redirect(['view', 'id' => $id]);
        }

        if (!in_array($status, [Application::STATUS_REJECTED, Application::STATUS_REVISION_REQUIRED], true)) {
            $completionErrors = $this->requiredSubmissionErrors($model);
            if ($completionErrors) {
                Yii::$app->session->setFlash('error', 'Belum dapat melanjutkan status: ' . implode(' ', $completionErrors));
                return $this->redirect(['view', 'id' => $id]);
            }
        }

        $oldStatus = $model->status;
        $model->status = $status;
        if ($status === Application::STATUS_VERIFIED) {
            $model->verified_by = Yii::$app->user->id;
            $model->verified_at = date('Y-m-d H:i:s');
        }
        $model->save(false);

        try {
            Yii::$app->db->createCommand()->insert('application_stage_history', [
                'application_id' => $model->id,
                'stage_code' => $status,
                'status' => 'completed',
                'note' => $note !== '' ? $note : ('Status diperbarui dari ' . $oldStatus . ' menjadi ' . $status . '.'),
                'changed_by' => Yii::$app->user->id,
                'created_at' => time(),
            ])->execute();
        } catch (\Throwable $e) {
            Yii::warning($e, __METHOD__);
        }

        if ($note !== '') {
            $internalNote = new ApplicationNote([
                'application_id' => $model->id,
                'user_id' => Yii::$app->user->id,
                'note' => $note,
                'is_internal' => 1,
                'created_at' => time(),
            ]);
            $internalNote->save(false);
        }

        Yii::$app->session->setFlash('success', 'Status pendaftar berhasil diperbarui.');
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionDocumentStatus($id, $documentId)
    {
        $document = ApplicationDocument::findOne(['id' => $documentId, 'application_id' => $id]);
        if (!$document) {
            throw new NotFoundHttpException('Dokumen tidak ditemukan.');
        }

        $status = (string)Yii::$app->request->post('verification_status');
        $note = trim((string)Yii::$app->request->post('verification_note', ''));
        if (!in_array($status, ['pending', 'valid', 'invalid'], true)) {
            Yii::$app->session->setFlash('error', 'Status dokumen tidak tersedia.');
            return $this->redirect(['view', 'id' => $id, '#' => 'dokumen']);
        }
        if ($status === 'invalid' && $note === '') {
            Yii::$app->session->setFlash('error', 'Tuliskan alasan dokumen perlu diperbaiki.');
            return $this->redirect(['view', 'id' => $id, '#' => 'dokumen']);
        }

        $document->verification_status = $status;
        $document->verification_note = $note !== '' ? $note : null;
        $document->verified_by = Yii::$app->user->id;
        $document->verified_at = date('Y-m-d H:i:s');
        $document->save(false);

        Yii::$app->session->setFlash('success', 'Hasil pemeriksaan dokumen berhasil disimpan.');
        return $this->redirect(['view', 'id' => $id, '#' => 'dokumen']);
    }

    public function actionNote($id)
    {
        $application = Application::findOne($id);
        if (!$application) {
            throw new NotFoundHttpException('Pendaftar tidak ditemukan.');
        }
        $noteText = trim((string)Yii::$app->request->post('note', ''));
        if ($noteText !== '') {
            $note = new ApplicationNote([
                'application_id' => $application->id,
                'user_id' => Yii::$app->user->id,
                'note' => $noteText,
                'is_internal' => 1,
                'created_at' => time(),
            ]);
            $note->save(false);
            Yii::$app->session->setFlash('success', 'Catatan verifikator ditambahkan.');
        }
        return $this->redirect(['view', 'id' => $id, '#' => 'catatan']);
    }

    private function allowedNextStatuses(string $current): array
    {
        return match ($current) {
            Application::STATUS_SUBMITTED => [Application::STATUS_REVISION_REQUIRED, Application::STATUS_VERIFIED, Application::STATUS_REJECTED],
            Application::STATUS_VERIFIED => [Application::STATUS_REVISION_REQUIRED, Application::STATUS_ADMIN_PASS, Application::STATUS_REJECTED],
            Application::STATUS_ADMIN_PASS => [Application::STATUS_INTERVIEW, Application::STATUS_REJECTED],
            Application::STATUS_INTERVIEW => [Application::STATUS_INTERVIEW_PASS, Application::STATUS_REJECTED],
            Application::STATUS_INTERVIEW_PASS => [Application::STATUS_FINAL_PASS, Application::STATUS_REJECTED],
            default => [],
        };
    }

    private function requiredSubmissionErrors(Application $application): array
    {
        $errors = [];
        $fields = RecruitmentFormField::find()->where([
            'batch_id' => $application->batch_id,
            'is_active' => 1,
            'is_required' => 1,
        ])->all();

        foreach ($fields as $field) {
            if ($field->isFileField()) {
                [$minFiles] = $this->fieldFileLimits($field);
                $validCount = ApplicationDocument::find()->where([
                    'application_id' => $application->id,
                    'field_id' => $field->id,
                    'verification_status' => 'valid',
                ])->count();
                if ((int)$validCount < $minFiles) {
                    $errors[] = 'Dokumen "' . $field->label . '" belum dinyatakan valid secara lengkap.';
                }
                continue;
            }

            $answer = ApplicationAnswer::findOne(['application_id' => $application->id, 'field_id' => $field->id]);
            if (!$answer || (trim((string)$answer->answer_text) === '' && trim((string)$answer->answer_json) === '')) {
                $errors[] = 'Pertanyaan "' . $field->label . '" belum dijawab.';
            }
        }
        return array_values(array_unique($errors));
    }

    private function groupDocuments(int $applicationId): array
    {
        $grouped = [];
        foreach (ApplicationDocument::find()->where(['application_id' => $applicationId])->orderBy(['id' => SORT_ASC])->all() as $row) {
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
}
