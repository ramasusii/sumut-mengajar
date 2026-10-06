<?php
namespace app\controllers;

use app\models\DocumentationAlbum;
use app\models\DocumentationPhoto;
use app\models\KabupatenKota;
use app\models\RecruitmentBatch;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class AdminDocumentationController extends Controller
{
    public $layout = 'main';

    private const MAX_UPLOAD_FILES = 10;
    private const MAX_UPLOAD_BYTES = 200 * 1024;
    private const MAIN_MAX_SIDE = 2000;
    private const THUMB_MAX_SIDE = 720;

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
                    throw new ForbiddenHttpException('Akun ini tidak memiliki akses untuk mengelola dokumentasi pengabdian.');
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'upload' => ['post'],
                    'photo-caption' => ['post'],
                    'set-cover' => ['post'],
                    'delete-photo' => ['post'],
                    'move-photo' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $q = trim((string) Yii::$app->request->get('q', ''));
        $status = trim((string) Yii::$app->request->get('status', ''));
        $year = (int) Yii::$app->request->get('year', 0);
        $regionId = (int) Yii::$app->request->get('region_id', 0);

        $query = DocumentationAlbum::find()
            ->with(['region', 'batch', 'coverPhoto', 'firstPhoto', 'creator']);

        if ($q !== '') {
            $query->andWhere(['or',
                ['like', 'documentation_album.title', $q],
                ['like', 'documentation_album.description', $q],
            ]);
        }
        if ($status !== '') {
            $query->andWhere(['documentation_album.status' => $status]);
        }
        if ($year > 0) {
            $query->andWhere(['documentation_album.year' => $year]);
        }
        if ($regionId > 0) {
            $query->andWhere(['documentation_album.kabupaten_kota_id' => $regionId]);
        }

        $pagination = new Pagination([
            'totalCount' => (clone $query)->count(),
            'pageSize' => 18,
            'pageSizeParam' => false,
        ]);

        $models = $query
            ->orderBy(['documentation_album.year' => SORT_DESC, 'documentation_album.id' => SORT_DESC])
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $regions = KabupatenKota::find()->where(['is_active' => 1])->orderBy(['nama' => SORT_ASC])->all();
        $years = DocumentationAlbum::find()
            ->select('year')
            ->distinct()
            ->orderBy(['year' => SORT_DESC])
            ->column();

        $stats = [
            'albums' => (int) DocumentationAlbum::find()->count(),
            'published' => (int) DocumentationAlbum::find()->where(['status' => DocumentationAlbum::STATUS_PUBLISHED])->count(),
            'photos' => (int) DocumentationPhoto::find()->count(),
        ];

        return $this->render('index', compact(
            'models', 'pagination', 'regions', 'years', 'stats', 'q', 'status', 'year', 'regionId'
        ));
    }

    public function actionCreate()
    {
        $model = new DocumentationAlbum([
            'status' => DocumentationAlbum::STATUS_DRAFT,
            'year' => (int) date('Y'),
        ]);

        return $this->saveAlbum($model);
    }

    public function actionUpdate($id)
    {
        return $this->saveAlbum($this->findAlbum($id));
    }

    public function actionPhotos($id)
    {
        $model = $this->findAlbum($id);
        $photos = DocumentationPhoto::find()
            ->where(['album_id' => $model->id])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])
            ->all();

        return $this->render('photos', [
            'model' => $model,
            'photos' => $photos,
            'maxUploadFiles' => self::MAX_UPLOAD_FILES,
            'maxUploadKb' => (int) (self::MAX_UPLOAD_BYTES / 1024),
        ]);
    }

    public function actionUpload($id)
    {
        $album = $this->findAlbum($id);
        $uploads = UploadedFile::getInstancesByName('photos');

        if (!$uploads) {
            Yii::$app->session->setFlash('warning', 'Pilih minimal satu foto untuk diunggah.');
            return $this->redirect(['photos', 'id' => $album->id]);
        }

        if (count($uploads) > self::MAX_UPLOAD_FILES) {
            Yii::$app->session->setFlash('error', 'Maksimal ' . self::MAX_UPLOAD_FILES . ' foto dalam satu kali unggah.');
            return $this->redirect(['photos', 'id' => $album->id]);
        }

        $currentMaxSort = (int) DocumentationPhoto::find()
            ->where(['album_id' => $album->id])
            ->max('sort_order');

        $hasCover = DocumentationPhoto::find()
            ->where(['album_id' => $album->id, 'is_cover' => 1])
            ->exists();

        $uploadedCount = 0;
        $errors = [];

        foreach ($uploads as $index => $upload) {
            try {
                $photo = $this->storePhoto($album, $upload, $currentMaxSort + $index + 1, !$hasCover && $uploadedCount === 0);
                if ($photo) {
                    $uploadedCount++;
                }
            } catch (\Throwable $e) {
                Yii::error($e, __METHOD__);
                $errors[] = $upload->name . ': ' . $e->getMessage();
            }
        }

        if ($uploadedCount > 0) {
            Yii::$app->session->setFlash('success', $uploadedCount . ' foto berhasil ditambahkan ke album.');
        }
        if ($errors) {
            Yii::$app->session->setFlash('warning', implode(' | ', $errors));
        }

        return $this->redirect(['photos', 'id' => $album->id]);
    }

    public function actionPhotoCaption($id, $photoId)
    {
        $album = $this->findAlbum($id);
        $photo = $this->findPhoto($album->id, $photoId);
        $photo->caption = trim((string) Yii::$app->request->post('caption', ''));

        if ($photo->save()) {
            Yii::$app->session->setFlash('success', 'Keterangan foto berhasil disimpan.');
        } else {
            Yii::$app->session->setFlash('error', 'Keterangan foto belum dapat disimpan.');
        }

        return $this->redirect(['photos', 'id' => $album->id]);
    }

    public function actionSetCover($id, $photoId)
    {
        $album = $this->findAlbum($id);
        $photo = $this->findPhoto($album->id, $photoId);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            DocumentationPhoto::updateAll(['is_cover' => 0], ['album_id' => $album->id]);
            $photo->is_cover = 1;
            if (!$photo->save(false, ['is_cover'])) {
                throw new \RuntimeException('Cover belum dapat diperbarui.');
            }
            $transaction->commit();
            Yii::$app->session->setFlash('success', 'Cover album berhasil diperbarui.');
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error($e, __METHOD__);
            Yii::$app->session->setFlash('error', 'Cover album belum dapat diperbarui.');
        }

        return $this->redirect(['photos', 'id' => $album->id]);
    }

    public function actionMovePhoto($id, $photoId, $direction)
    {
        $album = $this->findAlbum($id);
        $photo = $this->findPhoto($album->id, $photoId);

        $query = DocumentationPhoto::find()->where(['album_id' => $album->id]);
        if ($direction === 'up') {
            $neighbor = $query
                ->andWhere(['<', 'sort_order', $photo->sort_order])
                ->orderBy(['sort_order' => SORT_DESC, 'id' => SORT_DESC])
                ->one();
        } elseif ($direction === 'down') {
            $neighbor = $query
                ->andWhere(['>', 'sort_order', $photo->sort_order])
                ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])
                ->one();
        } else {
            throw new NotFoundHttpException('Arah urutan tidak valid.');
        }

        if ($neighbor) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $old = $photo->sort_order;
                $photo->sort_order = $neighbor->sort_order;
                $neighbor->sort_order = $old;
                $photo->save(false, ['sort_order']);
                $neighbor->save(false, ['sort_order']);
                $transaction->commit();
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::error($e, __METHOD__);
            }
        }

        return $this->redirect(['photos', 'id' => $album->id]);
    }

    public function actionDeletePhoto($id, $photoId)
    {
        $album = $this->findAlbum($id);
        $photo = $this->findPhoto($album->id, $photoId);
        $wasCover = (bool) $photo->is_cover;

        $this->deleteStoredFile($photo->file_path);
        $this->deleteStoredFile($photo->thumb_path);
        $photo->delete();

        if ($wasCover) {
            $next = DocumentationPhoto::find()
                ->where(['album_id' => $album->id])
                ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])
                ->one();
            if ($next) {
                $next->is_cover = 1;
                $next->save(false, ['is_cover']);
            }
        }

        Yii::$app->session->setFlash('success', 'Foto berhasil dihapus.');
        return $this->redirect(['photos', 'id' => $album->id]);
    }

    public function actionDelete($id)
    {
        $album = $this->findAlbum($id);
        foreach ($album->photos as $photo) {
            $this->deleteStoredFile($photo->file_path);
            $this->deleteStoredFile($photo->thumb_path);
        }

        $directory = Yii::getAlias('@webroot') . '/uploads/documentation/album-' . $album->id;
        if (is_dir($directory)) {
            FileHelper::removeDirectory($directory);
        }

        $album->delete();
        Yii::$app->session->setFlash('success', 'Album dokumentasi berhasil dihapus.');
        return $this->redirect(['index']);
    }

    private function saveAlbum(DocumentationAlbum $model)
    {
        if ($model->load(Yii::$app->request->post())) {
            if (!$model->slug) {
                $model->slug = $this->uniqueSlug($model->title . '-' . $model->year, $model->id);
            }

            if ($model->isNewRecord) {
                $model->created_by = Yii::$app->user->id;
            }

            if ($model->status === DocumentationAlbum::STATUS_PUBLISHED && !$model->published_at) {
                $model->published_at = date('Y-m-d H:i:s');
            }

            if ($model->status === DocumentationAlbum::STATUS_DRAFT) {
                $model->published_at = null;
            }

            if ($model->save()) {
                Yii::$app->session->setFlash(
                    'success',
                    $model->isNewRecord ? 'Album berhasil dibuat.' : 'Album berhasil diperbarui.'
                );

                return $model->getPhotoCount() === 0
                    ? $this->redirect(['photos', 'id' => $model->id])
                    : $this->redirect(['index']);
            }
        }

        $regions = KabupatenKota::find()
            ->where(['is_active' => 1])
            ->orderBy(['nama' => SORT_ASC])
            ->all();

        $batches = RecruitmentBatch::find()
            ->orderBy(['batch_number' => SORT_DESC, 'id' => SORT_DESC])
            ->all();

        return $this->render($model->isNewRecord ? 'create' : 'update', [
            'model' => $model,
            'regions' => $regions,
            'batches' => $batches,
        ]);
    }

    private function storePhoto(DocumentationAlbum $album, UploadedFile $upload, int $sortOrder, bool $isCover): DocumentationPhoto
    {
        if ($upload->error !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('unggahan tidak lengkap.');
        }

        if ($upload->size <= 0 || $upload->size > self::MAX_UPLOAD_BYTES) {
            throw new \RuntimeException('ukuran maksimal 200 KB per foto.');
        }

        if (!extension_loaded('gd')) {
            throw new \RuntimeException('fitur pengolah gambar server belum aktif.');
        }

        $mime = FileHelper::getMimeType($upload->tempName);
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($mime, $allowed, true)) {
            throw new \RuntimeException('format harus JPG, PNG, atau WEBP.');
        }

        [$source, $extension] = $this->createImageResource($upload->tempName, $mime);
        if (!$source) {
            throw new \RuntimeException('file gambar tidak dapat dibaca.');
        }

        if ($mime === 'image/jpeg') {
            $source = $this->fixJpegOrientation($source, $upload->tempName);
        }

        $albumDirRelative = 'uploads/documentation/album-' . (int) $album->id;
        $albumDirAbsolute = Yii::getAlias('@webroot') . '/' . $albumDirRelative;
        FileHelper::createDirectory($albumDirAbsolute, 0775, true);

        $token = strtolower(Yii::$app->security->generateRandomString(10));
        $token = preg_replace('/[^a-z0-9]+/', '', $token);
        $base = 'gsm-' . date('Ymd-His') . '-' . ($token ?: uniqid());

        $mainRelative = $albumDirRelative . '/' . $base . '.' . $extension;
        $thumbRelative = $albumDirRelative . '/' . $base . '-thumb.' . $extension;
        $mainAbsolute = Yii::getAlias('@webroot') . '/' . $mainRelative;
        $thumbAbsolute = Yii::getAlias('@webroot') . '/' . $thumbRelative;

        $main = $this->resizeImage($source, self::MAIN_MAX_SIDE);
        $thumb = $this->resizeImage($source, self::THUMB_MAX_SIDE);

        $this->saveImageResource($main, $mainAbsolute, $mime);
        $this->saveImageResource($thumb, $thumbAbsolute, $mime);

        $width = imagesx($main);
        $height = imagesy($main);

        imagedestroy($source);
        imagedestroy($main);
        imagedestroy($thumb);

        $photo = new DocumentationPhoto([
            'album_id' => $album->id,
            'file_path' => $mainRelative,
            'thumb_path' => $thumbRelative,
            'original_name' => $upload->name,
            'mime_type' => $mime,
            'file_size' => is_file($mainAbsolute) ? filesize($mainAbsolute) : $upload->size,
            'width' => $width,
            'height' => $height,
            'sort_order' => $sortOrder,
            'is_cover' => $isCover ? 1 : 0,
            'created_at' => time(),
        ]);

        if (!$photo->save()) {
            @unlink($mainAbsolute);
            @unlink($thumbAbsolute);
            throw new \RuntimeException('data foto belum dapat disimpan.');
        }

        return $photo;
    }

    private function createImageResource(string $path, string $mime): array
    {
        if ($mime === 'image/jpeg' && function_exists('imagecreatefromjpeg')) {
            return [@imagecreatefromjpeg($path), 'jpg'];
        }
        if ($mime === 'image/png' && function_exists('imagecreatefrompng')) {
            return [@imagecreatefrompng($path), 'png'];
        }
        if ($mime === 'image/webp' && function_exists('imagecreatefromwebp')) {
            return [@imagecreatefromwebp($path), 'webp'];
        }
        return [null, 'jpg'];
    }

    private function fixJpegOrientation($image, string $path)
    {
        if (!function_exists('exif_read_data')) {
            return $image;
        }

        try {
            $exif = @exif_read_data($path);
            $orientation = (int) ($exif['Orientation'] ?? 1);
            if ($orientation === 3) {
                $rotated = imagerotate($image, 180, 0);
            } elseif ($orientation === 6) {
                $rotated = imagerotate($image, -90, 0);
            } elseif ($orientation === 8) {
                $rotated = imagerotate($image, 90, 0);
            } else {
                return $image;
            }

            if ($rotated) {
                imagedestroy($image);
                return $rotated;
            }
        } catch (\Throwable $e) {
            Yii::warning($e->getMessage(), __METHOD__);
        }

        return $image;
    }

    private function resizeImage($source, int $maxSide)
    {
        $width = imagesx($source);
        $height = imagesy($source);
        $largest = max($width, $height);
        $scale = $largest > $maxSide ? $maxSide / $largest : 1;

        $newWidth = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));

        $target = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($target, false);
        imagesavealpha($target, true);
        $transparent = imagecolorallocatealpha($target, 255, 255, 255, 127);
        imagefilledrectangle($target, 0, 0, $newWidth, $newHeight, $transparent);

        imagecopyresampled(
            $target,
            $source,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $width,
            $height
        );

        return $target;
    }

    private function saveImageResource($image, string $path, string $mime): void
    {
        $ok = false;

        if ($mime === 'image/jpeg') {
            $ok = imagejpeg($image, $path, 84);
        } elseif ($mime === 'image/png') {
            $ok = imagepng($image, $path, 7);
        } elseif ($mime === 'image/webp' && function_exists('imagewebp')) {
            $ok = imagewebp($image, $path, 84);
        }

        if (!$ok) {
            throw new \RuntimeException('gambar hasil optimasi belum dapat disimpan.');
        }
    }

    private function uniqueSlug(string $value, $ignoreId = null): string
    {
        $base = strtolower(trim($value));
        $base = preg_replace('/[^a-z0-9]+/', '-', $base);
        $base = trim($base, '-');
        $base = $base ?: 'dokumentasi';

        $slug = $base;
        $i = 2;

        while (true) {
            $query = DocumentationAlbum::find()->where(['slug' => $slug]);
            if ($ignoreId) {
                $query->andWhere(['<>', 'id', $ignoreId]);
            }
            if (!$query->exists()) {
                return $slug;
            }
            $slug = $base . '-' . $i++;
        }
    }

    private function findAlbum($id): DocumentationAlbum
    {
        $model = DocumentationAlbum::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Album dokumentasi tidak ditemukan.');
        }
        return $model;
    }

    private function findPhoto($albumId, $photoId): DocumentationPhoto
    {
        $photo = DocumentationPhoto::findOne(['id' => $photoId, 'album_id' => $albumId]);
        if (!$photo) {
            throw new NotFoundHttpException('Foto dokumentasi tidak ditemukan.');
        }
        return $photo;
    }

    private function deleteStoredFile(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }

        $absolute = Yii::getAlias('@webroot') . '/' . ltrim($relativePath, '/');
        if (is_file($absolute)) {
            @unlink($absolute);
        }
    }
}
