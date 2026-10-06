<?php
namespace app\controllers;

use app\components\PhoneHelper;
use app\components\AlumniPhotoHelper;
use app\models\AlumniCareer;
use app\models\AlumniProfile;
use app\models\AlumniPublication;
use app\models\Application;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class AdminAlumniController extends Controller
{
    public $layout = 'main';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['developer', 'superAdmin', 'adminGsm']],
                ],
                'denyCallback' => function () {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['/site/staff-login']);
                    }
                    throw new ForbiddenHttpException('Akun ini tidak memiliki akses untuk mengelola alumni.');
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'sync-finalists' => ['post'],
                    'verify' => ['post'],
                    'delete-career' => ['post'],
                    'delete-publication' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $q = trim((string)Yii::$app->request->get('q', ''));
        $status = trim((string)Yii::$app->request->get('status', ''));
        $batch = (int)Yii::$app->request->get('batch', 0);

        $query = AlumniProfile::find()->with(['batch']);
        if ($q !== '') {
            $query->andWhere([
                'or',
                ['like', 'nama_lengkap', $q],
                ['like', 'whatsapp', $q],
                ['like', 'current_institution', $q],
                ['like', 'current_position', $q],
            ]);
        }
        if ($status !== '') {
            $query->andWhere(['verification_status' => $status]);
        }
        if ($batch > 0) {
            $query->andWhere(['batch_number' => $batch]);
        }

        $pagination = new Pagination([
            'totalCount' => (clone $query)->count(),
            'pageSize' => 20,
            'pageSizeParam' => false,
        ]);

        $models = $query
            ->orderBy(['verification_status' => SORT_ASC, 'batch_number' => SORT_DESC, 'id' => SORT_DESC])
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $batches = AlumniProfile::find()->select('batch_number')->distinct()->orderBy(['batch_number' => SORT_DESC])->column();
        $summary = [
            'total' => (int)AlumniProfile::find()->count(),
            'pending' => (int)AlumniProfile::find()->where(['verification_status' => AlumniProfile::STATUS_PENDING])->count(),
            'public' => (int)AlumniProfile::find()->where(['verification_status' => AlumniProfile::STATUS_VERIFIED, 'is_public' => 1, 'consent_public' => 1])->count(),
            'works' => (int)AlumniPublication::find()->count(),
        ];

        return $this->render('index', compact('models', 'pagination', 'batches', 'summary', 'q', 'status', 'batch'));
    }

    public function actionCreate()
    {
        $model = new AlumniProfile([
            'verification_status' => AlumniProfile::STATUS_VERIFIED,
            'consent_public' => 0,
            'is_public' => 0,
        ]);
        return $this->saveProfile($model);
    }

    public function actionUpdate($id)
    {
        return $this->saveProfile($this->findModel($id));
    }

    public function actionView($id)
    {
        $model = AlumniProfile::find()->where(['id' => $id])->with(['careers', 'allPublications', 'batch'])->one();
        if (!$model) {
            throw new NotFoundHttpException('Profil alumni tidak ditemukan.');
        }

        return $this->render('view', [
            'model' => $model,
            'career' => new AlumniCareer(['alumni_id' => $model->id, 'is_current' => 0, 'is_public' => 1]),
            'publication' => new AlumniPublication(['alumni_id' => $model->id, 'is_public' => 1]),
        ]);
    }

    public function actionVerify($id)
    {
        $model = $this->findModel($id);
        $status = (string)Yii::$app->request->post('verification_status');
        $isPublic = (int)Yii::$app->request->post('is_public', 0);
        $featured = (int)Yii::$app->request->post('is_featured', 0);

        if (!in_array($status, [AlumniProfile::STATUS_PENDING, AlumniProfile::STATUS_VERIFIED, AlumniProfile::STATUS_REJECTED], true)) {
            Yii::$app->session->setFlash('error', 'Status verifikasi tidak valid.');
            return $this->redirect(['view', 'id' => $id]);
        }

        if ($isPublic && !$model->consent_public) {
            Yii::$app->session->setFlash('error', 'Profil belum dapat dipublikasikan karena persetujuan alumni belum tercatat.');
            return $this->redirect(['view', 'id' => $id]);
        }

        if ($status === AlumniProfile::STATUS_VERIFIED) {
            $this->linkKnownApplication($model);
        }

        $model->verification_status = $status;
        $model->is_public = ($status === AlumniProfile::STATUS_VERIFIED) ? $isPublic : 0;
        $model->is_featured = ($model->is_public) ? $featured : 0;
        $model->verified_by = Yii::$app->user->id;
        $model->verified_at = date('Y-m-d H:i:s');
        $model->save(false);

        Yii::$app->session->setFlash('success', 'Status alumni berhasil diperbarui.');
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionSyncFinalists()
    {
        $applications = Application::find()
            ->where(['status' => Application::STATUS_FINAL_PASS])
            ->with(['user', 'profile', 'batch.kabupatenKota'])
            ->all();

        $created = 0;
        $skipped = 0;

        foreach ($applications as $application) {
            if (AlumniProfile::find()->where(['application_id' => $application->id])->exists()) {
                $skipped++;
                continue;
            }

            $phone = PhoneHelper::normalizeIndonesia($application->user->whatsapp ?: ($application->profile->nomor_whatsapp ?? null));
            if ($phone && AlumniProfile::find()->where(['whatsapp' => $phone])->exists()) {
                $skipped++;
                continue;
            }

            $profile = new AlumniProfile();
            $profile->user_id = $application->user_id;
            $profile->application_id = $application->id;
            $profile->batch_id = $application->batch_id;
            $profile->batch_number = (int)$application->batch->batch_number;
            $profile->batch_year = $application->batch->activity_start
                ? (int)date('Y', strtotime($application->batch->activity_start))
                : (int)date('Y', $application->created_at);
            $profile->location_name = $application->batch->kabupatenKota
                ? $application->batch->kabupatenKota->label
                : 'Sumatera Utara';
            $profile->nama_lengkap = $application->profile->nama_lengkap ?? $application->user->nama ?? 'Alumni Sumut Mengajar';
            $profile->whatsapp = $phone ?: ('legacy-' . $application->id);
            $profile->current_position = $application->profile->pekerjaan ?? null;
            $profile->instagram = $application->profile->instagram ?? null;
            $profile->verification_status = AlumniProfile::STATUS_VERIFIED;
            $profile->consent_public = 0;
            $profile->is_public = 0;
            $profile->is_featured = 0;
            $profile->verified_by = Yii::$app->user->id;
            $profile->verified_at = date('Y-m-d H:i:s');
            $profile->slug = $this->uniqueSlug($profile->nama_lengkap, $profile->batch_number);

            if ($profile->save(false)) {
                $created++;
            } else {
                $skipped++;
            }
        }

        Yii::$app->session->setFlash('success', "Sinkronisasi selesai. {$created} profil dibuat, {$skipped} dilewati. Profil hasil sinkronisasi belum dipublikasikan sampai persetujuan alumni dicatat.");
        return $this->redirect(['index']);
    }

    public function actionAddCareer($id)
    {
        $alumni = $this->findModel($id);
        $career = new AlumniCareer(['alumni_id' => $alumni->id]);
        if ($career->load(Yii::$app->request->post()) && $career->save()) {
            if ($career->is_current) {
                AlumniCareer::updateAll(['is_current' => 0], ['and', ['alumni_id' => $alumni->id], ['<>', 'id', $career->id]]);
                $alumni->current_position = $career->position_title;
                $alumni->current_institution = $career->institution_name;
                $alumni->sector = $career->sector;
                $alumni->work_city = $career->city;
                $alumni->save(false, ['current_position', 'current_institution', 'sector', 'work_city', 'updated_at']);
            }
            Yii::$app->session->setFlash('success', 'Riwayat karier berhasil ditambahkan.');
        } else {
            Yii::$app->session->setFlash('error', 'Riwayat karier belum dapat disimpan.');
        }
        return $this->redirect(['view', 'id' => $id, '#' => 'karier']);
    }

    public function actionDeleteCareer($id, $careerId)
    {
        $career = AlumniCareer::findOne(['id' => $careerId, 'alumni_id' => $id]);
        if ($career) {
            $career->delete();
        }
        return $this->redirect(['view', 'id' => $id, '#' => 'karier']);
    }

    public function actionAddPublication($id)
    {
        $alumni = $this->findModel($id);
        $publication = new AlumniPublication(['alumni_id' => $alumni->id, 'is_public' => 1]);
        if ($publication->load(Yii::$app->request->post())) {
            $publication->created_at = time();
            $publication->updated_at = time();
            if ($publication->save()) {
                Yii::$app->session->setFlash('success', 'Karya alumni berhasil ditambahkan.');
            } else {
                Yii::$app->session->setFlash('error', 'Karya alumni belum dapat disimpan. Periksa kembali data.');
            }
        }
        return $this->redirect(['view', 'id' => $id, '#' => 'karya']);
    }

    public function actionDeletePublication($id, $publicationId)
    {
        $publication = AlumniPublication::findOne(['id' => $publicationId, 'alumni_id' => $id]);
        if ($publication) {
            $publication->delete();
        }
        return $this->redirect(['view', 'id' => $id, '#' => 'karya']);
    }

    private function saveProfile(AlumniProfile $model)
    {
        $oldPhoto = $model->photo;
        if ($model->load(Yii::$app->request->post())) {
            $model->whatsapp = PhoneHelper::normalizeIndonesia($model->whatsapp) ?: $model->whatsapp;
            if (!$model->slug) {
                $model->slug = $this->uniqueSlug($model->nama_lengkap, (int)$model->batch_number);
            }

            $photo = UploadedFile::getInstanceByName('photo_upload');
            if ($photo) {
                try {
                    $model->photo = $this->savePhoto($photo);
                } catch (\Throwable $e) {
                    $model->addError('photo', $e->getMessage());
                }
            }

            if (!$model->hasErrors() && $model->save()) {
                if ($photo && $oldPhoto && $oldPhoto !== $model->photo) {
                    $this->deletePhoto($oldPhoto);
                }
                Yii::$app->session->setFlash('success', 'Profil alumni berhasil disimpan.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render($model->isNewRecord ? 'create' : 'update', ['model' => $model]);
    }

    private function savePhoto(UploadedFile $file): string
    {
        return AlumniPhotoHelper::saveNormalized($file);
    }

    private function deletePhoto(?string $path): void
    {
        if (!$path || !str_starts_with($path, 'web/uploads/alumni/')) {
            return;
        }
        $absolute = Yii::getAlias('@app/' . ltrim($path, '/'));
        if (is_file($absolute)) {
            @unlink($absolute);
        }
    }

    private function uniqueSlug(string $name, int $batch): string
    {
        $base = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-')) ?: 'alumni';
        $base .= '-batch-' . max(1, $batch);
        $slug = $base;
        $i = 2;
        while (AlumniProfile::find()->where(['slug' => $slug])->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    private function linkKnownApplication(AlumniProfile $model): void
    {
        $phone = PhoneHelper::normalizeIndonesia($model->whatsapp);
        if (!$phone) {
            return;
        }

        $user = \app\models\User::findByWhatsapp($phone);
        if ($user && !$model->user_id) {
            $model->user_id = $user->id;
        }

        if ($user && !$model->application_id) {
            $application = Application::find()
                ->alias('a')
                ->joinWith(['batch b'])
                ->where([
                    'a.user_id' => $user->id,
                    'a.status' => Application::STATUS_FINAL_PASS,
                    'b.batch_number' => (int)$model->batch_number,
                ])
                ->with(['batch.kabupatenKota'])
                ->orderBy(['a.id' => SORT_DESC])
                ->one();

            if ($application) {
                $model->application_id = $application->id;
                $model->batch_id = $application->batch_id;
                if (!$model->batch_year) {
                    $model->batch_year = $application->batch->activity_start
                        ? (int)date('Y', strtotime($application->batch->activity_start))
                        : (int)date('Y', $application->created_at);
                }
                if (!$model->location_name && $application->batch->kabupatenKota) {
                    $model->location_name = $application->batch->kabupatenKota->label;
                }
            }
        }
    }

    private function findModel($id): AlumniProfile
    {
        $model = AlumniProfile::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Profil alumni tidak ditemukan.');
        }
        return $model;
    }
}
