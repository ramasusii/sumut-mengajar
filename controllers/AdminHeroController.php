<?php
namespace app\controllers;

use app\models\HeroSlide;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class AdminHeroController extends Controller
{
    public $layout = 'main';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['superAdmin', 'adminGsm']],
                ],
                'denyCallback' => function () {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['/site/staff-login']);
                    }
                    throw new \yii\web\ForbiddenHttpException('Akun ini tidak memiliki akses untuk mengelola banner.');
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'toggle' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $models = HeroSlide::find()->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_DESC])->all();
        return $this->render('index', ['models' => $models]);
    }

    public function actionCreate()
    {
        $model = new HeroSlide(['is_published' => 1, 'sort_order' => 10]);
        return $this->saveModel($model);
    }

    public function actionUpdate($id)
    {
        return $this->saveModel($this->findModel($id));
    }

    public function actionToggle($id)
    {
        $model = $this->findModel($id);
        $model->is_published = $model->is_published ? 0 : 1;
        $model->save(false, ['is_published', 'updated_at']);
        return $this->redirect(['index']);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $this->deletePublicImage($model->desktop_image);
        $this->deletePublicImage($model->mobile_image);
        $model->delete();
        Yii::$app->session->setFlash('success', 'Banner berhasil dihapus.');
        return $this->redirect(['index']);
    }

    private function saveModel(HeroSlide $model)
    {
        $oldDesktop = $model->desktop_image;
        $oldMobile = $model->mobile_image;

        if ($model->load(Yii::$app->request->post())) {
            $model->start_at = $this->normalizeDateTimeLocal($model->start_at);
            $model->end_at = $this->normalizeDateTimeLocal($model->end_at);

            $desktop = UploadedFile::getInstanceByName('desktop_upload');
            $mobile = UploadedFile::getInstanceByName('mobile_upload');

            try {
                if ($desktop) {
                    $model->desktop_image = $this->savePublicImage($desktop, 'desktop');
                }
                if ($mobile) {
                    $model->mobile_image = $this->savePublicImage($mobile, 'mobile');
                }

                if (!$model->desktop_image) {
                    $model->addError('desktop_image', 'Banner desktop wajib tersedia.');
                }

                $model->created_by = $model->created_by ?: Yii::$app->user->id;

                if (!$model->hasErrors() && $model->save()) {
                    if ($desktop && $oldDesktop && $oldDesktop !== $model->desktop_image) {
                        $this->deletePublicImage($oldDesktop);
                    }
                    if ($mobile && $oldMobile && $oldMobile !== $model->mobile_image) {
                        $this->deletePublicImage($oldMobile);
                    }
                    Yii::$app->session->setFlash('success', 'Hero banner berhasil disimpan.');
                    return $this->redirect(['index']);
                }
            } catch (\Throwable $e) {
                Yii::error($e, __METHOD__);
                $model->addError('desktop_image', $e->getMessage());
            }
        }

        return $this->render($model->isNewRecord ? 'create' : 'update', ['model' => $model]);
    }

    private function savePublicImage(UploadedFile $file, string $prefix): string
    {
        if ($file->size <= 0 || $file->size > 5 * 1024 * 1024) {
            throw new \RuntimeException('Ukuran banner maksimal 5 MB.');
        }

        $info = @getimagesize($file->tempName);
        if (!$info || !in_array($info['mime'], ['image/jpeg', 'image/png', 'image/webp'], true)) {
            throw new \RuntimeException('Banner harus berupa JPG, PNG, atau WebP.');
        }

        $extension = match ($info['mime']) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        $dir = Yii::getAlias('@app/web/uploads/hero');
        FileHelper::createDirectory($dir, 0775, true);
        $name = $prefix . '-' . time() . '-' . Yii::$app->security->generateRandomString(7) . '.' . $extension;

        if (!$file->saveAs($dir . '/' . $name)) {
            throw new \RuntimeException('Banner belum dapat disimpan.');
        }

        return 'web/uploads/hero/' . $name;
    }

    private function deletePublicImage(?string $path): void
    {
        if (!$path || !str_starts_with($path, 'web/uploads/hero/')) {
            return;
        }
        $absolute = Yii::getAlias('@app/' . ltrim($path, '/'));
        if (is_file($absolute)) {
            @unlink($absolute);
        }
    }

    private function normalizeDateTimeLocal($value): ?string
    {
        $value = trim((string)$value);
        if ($value === '') return null;
        $timestamp = strtotime($value);
        return $timestamp ? date('Y-m-d H:i:s', $timestamp) : $value;
    }

    private function findModel($id): HeroSlide
    {
        $model = HeroSlide::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Banner tidak ditemukan.');
        }
        return $model;
    }
}
