<?php
namespace app\controllers;

use app\models\Post;
use app\models\PostCategory;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class AdminPostController extends Controller
{
    public $layout = 'main';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['developer','superAdmin','adminGsm']],
                ],
                'denyCallback' => function () {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['/site/staff-login']);
                    }
                    throw new ForbiddenHttpException('Akun ini tidak memiliki akses untuk mengelola artikel.');
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $q = trim((string)Yii::$app->request->get('q', ''));
        $status = trim((string)Yii::$app->request->get('status', ''));

        $query = Post::find()->with(['category','creator']);

        if ($q !== '') {
            $query->andWhere(['or',
                ['like', 'title', $q],
                ['like', 'excerpt', $q],
            ]);
        }

        if ($status !== '') {
            $query->andWhere(['status' => $status]);
        }

        $pagination = new Pagination([
            'totalCount' => (clone $query)->count(),
            'pageSize' => 20,
            'pageSizeParam' => false,
        ]);

        $models = $query
            ->orderBy(['id' => SORT_DESC])
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('index', compact('models','pagination','q','status'));
    }

    public function actionCreate()
    {
        $model = new Post([
            'status' => Post::STATUS_DRAFT,
        ]);

        return $this->saveModel($model);
    }

    public function actionUpdate($id)
    {
        return $this->saveModel($this->findModel($id));
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $this->deleteCover($model->cover_image);
        $model->delete();

        Yii::$app->session->setFlash('success', 'Artikel berhasil dihapus.');
        return $this->redirect(['index']);
    }

    private function saveModel(Post $model)
    {
        $oldCover = $model->cover_image;

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->slug) {
                $model->slug = $this->uniqueSlug($model->title, $model->id);
            } else {
                $model->slug = $this->uniqueSlug($model->slug, $model->id);
            }

            if ($model->isNewRecord) {
                $model->created_by = Yii::$app->user->id;
            }

            if ($model->status === Post::STATUS_PUBLISHED && !$model->published_at) {
                $model->published_at = date('Y-m-d H:i:s');
            }

            $upload = UploadedFile::getInstanceByName('cover_upload');
            if ($upload) {
                $ext = strtolower($upload->extension);
                if (!in_array($ext, ['jpg','jpeg','png','webp'], true)) {
                    $model->addError('cover_image', 'Cover harus JPG, PNG, atau WEBP.');
                } elseif ($upload->size > 5 * 1024 * 1024) {
                    $model->addError('cover_image', 'Ukuran cover maksimal 5 MB.');
                } else {
                    $dir = Yii::getAlias('@webroot') . '/uploads/posts';
                    FileHelper::createDirectory($dir, 0775, true);
                    $name = 'post-' . time() . '-' . Yii::$app->security->generateRandomString(6) . '.' . $ext;

                    if ($upload->saveAs($dir . '/' . $name)) {
                        $model->cover_image = 'uploads/posts/' . $name;
                    }
                }
            }

            if (!$model->hasErrors() && $model->save()) {
                if ($oldCover && $oldCover !== $model->cover_image) {
                    $this->deleteCover($oldCover);
                }

                Yii::$app->session->setFlash('success', 'Artikel berhasil disimpan.');
                return $this->redirect(['index']);
            }
        }

        $categories = PostCategory::find()->orderBy(['name' => SORT_ASC])->all();

        return $this->render($model->isNewRecord ? 'create' : 'update', [
            'model' => $model,
            'categories' => $categories,
        ]);
    }

    private function findModel($id): Post
    {
        $model = Post::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Artikel tidak ditemukan.');
        }
        return $model;
    }

    private function uniqueSlug(string $value, $ignoreId = null): string
    {
        $base = strtolower(trim($value));
        $base = preg_replace('/[^a-z0-9]+/', '-', $base);
        $base = trim($base, '-');
        $base = $base ?: 'artikel';

        $slug = $base;
        $i = 2;

        while (true) {
            $query = Post::find()->where(['slug' => $slug]);
            if ($ignoreId) {
                $query->andWhere(['<>', 'id', $ignoreId]);
            }
            if (!$query->exists()) {
                return $slug;
            }
            $slug = $base . '-' . $i++;
        }
    }

    private function deleteCover(?string $path): void
    {
        if (!$path) {
            return;
        }

        $absolute = Yii::getAlias('@webroot') . '/' . ltrim($path, '/');
        if (is_file($absolute)) {
            @unlink($absolute);
        }
    }
}
