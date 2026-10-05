<?php
namespace app\commands;

use app\components\PhoneHelper;
use app\models\ApplicantProfile;
use app\models\ApplicationDocument;
use app\models\User;
use Yii;
use yii\console\Controller;
use yii\helpers\FileHelper;

class MaintenanceController extends Controller
{
    /**
     * Normalisasi dan isi user.whatsapp dari profil peserta lama.
     * Jalankan preview: php yii maintenance/backfill-whatsapp
     * Terapkan:          php yii maintenance/backfill-whatsapp --apply=1
     */
    public function actionBackfillWhatsapp($apply = 0)
    {
        $apply = (bool)$apply;
        $updated = 0;
        $skipped = 0;
        $conflicts = 0;

        $profiles = ApplicantProfile::find()->with('user')->all();
        foreach ($profiles as $profile) {
            if (!$profile->user) {
                $skipped++;
                continue;
            }

            $normalized = PhoneHelper::normalizeIndonesia((string)$profile->nomor_whatsapp);
            if (!$normalized) {
                $this->stderr("SKIP user {$profile->user_id}: nomor WA tidak valid.\n");
                $skipped++;
                continue;
            }

            $owner = User::find()
                ->where(['whatsapp' => $normalized])
                ->andWhere(['<>', 'id', $profile->user_id])
                ->one();

            if ($owner) {
                $this->stderr("CONFLICT {$normalized}: sudah dipakai user {$owner->id}.\n");
                $conflicts++;
                continue;
            }

            if ($profile->user->whatsapp === $normalized && $profile->nomor_whatsapp === $normalized) {
                $skipped++;
                continue;
            }

            $this->stdout(($apply ? 'UPDATE' : 'PREVIEW') . " user {$profile->user_id} -> {$normalized}\n");
            if ($apply) {
                $tx = Yii::$app->db->beginTransaction();
                try {
                    $profile->nomor_whatsapp = $normalized;
                    $profile->save(false, ['nomor_whatsapp']);
                    $profile->user->whatsapp = $normalized;
                    $profile->user->save(false, ['whatsapp', 'updated_at']);
                    $tx->commit();
                } catch (\Throwable $e) {
                    $tx->rollBack();
                    $this->stderr("GAGAL user {$profile->user_id}: {$e->getMessage()}\n");
                    $conflicts++;
                    continue;
                }
            }
            $updated++;
        }

        $this->stdout("\nSelesai. " . ($apply ? 'Diperbarui' : 'Akan diperbarui') . ": {$updated}; dilewati: {$skipped}; konflik: {$conflicts}.\n");
        if (!$apply) {
            $this->stdout("Ini masih preview. Jika sudah sesuai, jalankan ulang dengan --apply=1.\n");
        }
    }

    /**
     * Pindahkan dokumen peserta lama dari uploads/ ke storage/private/.
     * Preview: php yii maintenance/migrate-private-documents
     * Terapkan: php yii maintenance/migrate-private-documents --apply=1
     */
    public function actionMigratePrivateDocuments($apply = 0)
    {
        $apply = (bool)$apply;
        $moved = 0;
        $alreadyPrivate = 0;
        $missing = 0;
        $failed = 0;

        $documents = ApplicationDocument::find()->orderBy(['id' => SORT_ASC])->all();
        foreach ($documents as $document) {
            $path = ltrim((string)$document->file_path, '/');
            if (str_starts_with($path, 'private/')) {
                $alreadyPrivate++;
                continue;
            }

            $source = $document->getAbsolutePath();
            if (!$source || !is_file($source)) {
                $this->stderr("MISSING document {$document->id}: {$document->file_path}\n");
                $missing++;
                continue;
            }

            $targetDir = Yii::getAlias('@app/storage/private/applications/' . $document->application_id);
            FileHelper::createDirectory($targetDir, 0775, true);

            $baseName = basename($source);
            if ($baseName === '' || $baseName === '.' || $baseName === '..') {
                $baseName = 'document-' . $document->id;
            }
            $target = $targetDir . '/' . $baseName;
            if (is_file($target) && realpath($target) !== realpath($source)) {
                $ext = pathinfo($baseName, PATHINFO_EXTENSION);
                $stem = pathinfo($baseName, PATHINFO_FILENAME);
                $target = $targetDir . '/' . $stem . '-legacy-' . $document->id . ($ext ? '.' . $ext : '');
            }

            $newPath = 'private/applications/' . $document->application_id . '/' . basename($target);
            $this->stdout(($apply ? 'MOVE' : 'PREVIEW') . " document {$document->id}: {$document->file_path} -> {$newPath}\n");

            if (!$apply) {
                $moved++;
                continue;
            }

            try {
                if (!@rename($source, $target)) {
                    if (!@copy($source, $target)) {
                        throw new \RuntimeException('File tidak dapat dipindahkan.');
                    }
                    @unlink($source);
                }

                $document->file_path = $newPath;
                $document->save(false, ['file_path']);
                $moved++;
            } catch (\Throwable $e) {
                $this->stderr("GAGAL document {$document->id}: {$e->getMessage()}\n");
                $failed++;
            }
        }

        $this->stdout("\nSelesai. " . ($apply ? 'Dipindahkan' : 'Akan dipindahkan') . ": {$moved}; sudah private: {$alreadyPrivate}; file hilang: {$missing}; gagal: {$failed}.\n");
        if (!$apply) {
            $this->stdout("Ini masih preview. Jika sudah sesuai, jalankan ulang dengan --apply=1.\n");
        }
    }
}
