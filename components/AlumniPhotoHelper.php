<?php
namespace app\components;

use Yii;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class AlumniPhotoHelper
{
    public const MAX_BYTES = 200 * 1024;
    public const TARGET_WIDTH = 354;
    public const TARGET_HEIGHT = 472;
    public const TARGET_RATIO = 3 / 4;
    public const RATIO_TOLERANCE = 0.015;

    public static function validateUpload(UploadedFile $file): void
    {
        if ($file->error !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Foto tidak berhasil diunggah.');
        }

        if ($file->size <= 0 || $file->size > self::MAX_BYTES) {
            throw new \RuntimeException('Ukuran foto maksimal 200 KB.');
        }

        $info = @getimagesize($file->tempName);
        if (!$info || !in_array($info['mime'], ['image/jpeg', 'image/png'], true)) {
            throw new \RuntimeException('Foto harus berupa JPG atau PNG.');
        }

        [$width, $height] = self::orientedDimensions(
            (int) $info[0],
            (int) $info[1],
            $file->tempName,
            $info['mime']
        );

        if ($width < self::TARGET_WIDTH || $height < self::TARGET_HEIGHT) {
            throw new \RuntimeException(
                'Resolusi foto minimal ' . self::TARGET_WIDTH . ' × ' . self::TARGET_HEIGHT . ' piksel.'
            );
        }

        if ($height <= 0) {
            throw new \RuntimeException('Ukuran foto tidak valid.');
        }

        $ratio = $width / $height;
        if (abs($ratio - self::TARGET_RATIO) > self::RATIO_TOLERANCE) {
            throw new \RuntimeException(
                'Foto harus menggunakan rasio pas foto 3:4 (portrait). Contoh: 354 × 472 piksel.'
            );
        }
    }

    public static function saveNormalized(UploadedFile $file): string
    {
        self::validateUpload($file);

        if (!extension_loaded('gd')) {
            throw new \RuntimeException('Fitur pengolah gambar server belum aktif.');
        }

        $info = @getimagesize($file->tempName);
        $mime = $info['mime'] ?? null;

        if ($mime === 'image/jpeg') {
            $source = @imagecreatefromjpeg($file->tempName);
        } elseif ($mime === 'image/png') {
            $source = @imagecreatefrompng($file->tempName);
        } else {
            $source = false;
        }

        if (!$source) {
            throw new \RuntimeException('Foto tidak dapat diproses.');
        }

        if ($mime === 'image/jpeg') {
            $source = self::fixJpegOrientation($source, $file->tempName);
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);

        $target = imagecreatetruecolor(self::TARGET_WIDTH, self::TARGET_HEIGHT);
        $white = imagecolorallocate($target, 255, 255, 255);
        imagefill($target, 0, 0, $white);

        imagecopyresampled(
            $target,
            $source,
            0,
            0,
            0,
            0,
            self::TARGET_WIDTH,
            self::TARGET_HEIGHT,
            $sourceWidth,
            $sourceHeight
        );

        $dir = Yii::getAlias('@app/web/uploads/alumni');
        FileHelper::createDirectory($dir, 0775, true);

        $token = strtolower(Yii::$app->security->generateRandomString(8));
        $token = preg_replace('/[^a-z0-9]+/', '', $token);
        $name = 'alumni-' . time() . '-' . ($token ?: uniqid()) . '.jpg';
        $absolute = $dir . '/' . $name;

        $quality = 86;
        $saved = imagejpeg($target, $absolute, $quality);

        imagedestroy($source);
        imagedestroy($target);

        if (!$saved) {
            throw new \RuntimeException('Foto belum dapat disimpan.');
        }

        // Jaga hasil akhir tetap ringan. Turunkan kualitas bila perlu.
        while (is_file($absolute) && filesize($absolute) > self::MAX_BYTES && $quality > 55) {
            $quality -= 5;
            $reloaded = @imagecreatefromjpeg($absolute);
            if (!$reloaded) {
                break;
            }
            imagejpeg($reloaded, $absolute, $quality);
            imagedestroy($reloaded);
            clearstatcache(true, $absolute);
        }

        if (!is_file($absolute) || filesize($absolute) > self::MAX_BYTES) {
            @unlink($absolute);
            throw new \RuntimeException('Foto belum dapat dioptimalkan hingga maksimal 200 KB.');
        }

        return 'web/uploads/alumni/' . $name;
    }

    private static function orientedDimensions(int $width, int $height, string $path, string $mime): array
    {
        if ($mime !== 'image/jpeg' || !function_exists('exif_read_data')) {
            return [$width, $height];
        }

        try {
            $exif = @exif_read_data($path);
            $orientation = (int) ($exif['Orientation'] ?? 1);
            if (in_array($orientation, [5, 6, 7, 8], true)) {
                return [$height, $width];
            }
        } catch (\Throwable $e) {
            Yii::warning($e->getMessage(), __METHOD__);
        }

        return [$width, $height];
    }

    private static function fixJpegOrientation($image, string $path)
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
}
