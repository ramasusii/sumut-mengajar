<?php
namespace app\models;

use app\components\PhoneHelper;
use app\components\AlumniPhotoHelper;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class AlumniRegistrationForm extends Model
{
    public $nama_lengkap;
    public $whatsapp;
    public $batch_number;
    public $batch_year;
    public $location_name;
    public $current_position;
    public $current_institution;
    public $sector;
    public $work_city;
    public $instagram;
    public $linkedin;
    public $bio;
    public $publication_title;
    public $publication_type;
    public $publication_year;
    public $publication_url;
    public $publication_about_service = 0;
    public $consent_public = 0;
    public $photo;

    public function rules()
    {
        return [
            [['nama_lengkap', 'whatsapp', 'batch_number', 'location_name', 'consent_public'], 'required'],
            [['batch_number', 'batch_year', 'publication_year'], 'integer', 'min' => 1],
            [['publication_about_service', 'consent_public'], 'boolean'],
            [['bio'], 'string', 'max' => 1500],
            [['nama_lengkap', 'location_name', 'current_position', 'current_institution', 'sector', 'work_city'], 'string', 'max' => 180],
            [['instagram', 'linkedin', 'publication_url'], 'string', 'max' => 500],
            [['linkedin', 'publication_url'], 'url', 'defaultScheme' => 'https', 'skipOnEmpty' => true],
            [['publication_title'], 'string', 'max' => 255],
            [['publication_type'], 'in', 'range' => array_keys(AlumniPublication::TYPES), 'skipOnEmpty' => true],
            ['whatsapp', 'validateWhatsapp'],
            ['consent_public', 'compare', 'compareValue' => 1, 'operator' => '==', 'message' => 'Persetujuan publikasi profil wajib diberikan untuk pendaftaran alumni.'],
            ['photo', 'file', 'extensions' => ['jpg', 'jpeg', 'png'], 'maxSize' => AlumniPhotoHelper::MAX_BYTES, 'skipOnEmpty' => true, 'checkExtensionByMimeType' => true],
        ];
    }

    public function validateWhatsapp($attribute)
    {
        $normalized = PhoneHelper::normalizeIndonesia($this->$attribute);
        if (!$normalized) {
            $this->addError($attribute, 'Masukkan nomor WhatsApp Indonesia yang valid.');
            return;
        }
        $this->$attribute = $normalized;

        if (AlumniProfile::find()->where(['whatsapp' => $normalized])->exists()) {
            $this->addError($attribute, 'Nomor WhatsApp ini sudah terdaftar sebagai alumni. Hubungi admin bila ingin memperbarui profil.');
        }
    }

    public function submit(): ?AlumniProfile
    {
        $this->photo = UploadedFile::getInstance($this, 'photo');
        if (!$this->validate()) {
            return null;
        }

        $tx = Yii::$app->db->beginTransaction();
        $savedPhotoAbsolute = null;
        try {
            $this->linkedin = $this->normalizeExternalUrl($this->linkedin);
            $this->publication_url = $this->normalizeExternalUrl($this->publication_url);

            $profile = new AlumniProfile();
            $profile->nama_lengkap = trim($this->nama_lengkap);
            $profile->whatsapp = $this->whatsapp;
            $profile->batch_number = (int)$this->batch_number;
            $profile->batch_year = $this->batch_year ? (int)$this->batch_year : null;
            $profile->location_name = trim($this->location_name);
            $profile->current_position = trim((string)$this->current_position) ?: null;
            $profile->current_institution = trim((string)$this->current_institution) ?: null;
            $profile->sector = trim((string)$this->sector) ?: null;
            $profile->work_city = trim((string)$this->work_city) ?: null;
            $profile->instagram = trim((string)$this->instagram) ?: null;
            $profile->linkedin = trim((string)$this->linkedin) ?: null;
            $profile->bio = trim((string)$this->bio) ?: null;
            $profile->consent_public = 1;
            $profile->is_public = 0;
            $profile->is_featured = 0;
            $profile->verification_status = AlumniProfile::STATUS_PENDING;
            $profile->slug = $this->uniqueSlug($profile->nama_lengkap, $profile->batch_number);

            if ($this->photo) {
                $profile->photo = $this->savePhoto($this->photo);
                $savedPhotoAbsolute = Yii::getAlias('@app/' . ltrim($profile->photo, '/'));
            }

            if (!$profile->save()) {
                throw new \RuntimeException('Profil alumni belum dapat disimpan.');
            }

            if ($profile->current_institution || $profile->current_position) {
                $career = new AlumniCareer([
                    'alumni_id' => $profile->id,
                    'institution_name' => $profile->current_institution ?: 'Belum diinformasikan',
                    'position_title' => $profile->current_position ?: 'Belum diinformasikan',
                    'sector' => $profile->sector,
                    'city' => $profile->work_city,
                    'start_year' => null,
                    'end_year' => null,
                    'is_current' => 1,
                    'is_public' => 1,
                ]);
                $career->save(false);
            }

            if (trim((string)$this->publication_title) !== '') {
                $publication = new AlumniPublication([
                    'alumni_id' => $profile->id,
                    'title' => trim($this->publication_title),
                    'publication_type' => $this->publication_type ?: 'other',
                    'publication_year' => $this->publication_year ?: null,
                    'url' => trim((string)$this->publication_url) ?: null,
                    'is_about_service' => (int)$this->publication_about_service,
                    'is_public' => 1,
                    'created_at' => time(),
                    'updated_at' => time(),
                ]);
                $publication->save(false);
            }

            $tx->commit();
            return $profile;
        } catch (\Throwable $e) {
            $tx->rollBack();
            if ($savedPhotoAbsolute && is_file($savedPhotoAbsolute)) {
                @unlink($savedPhotoAbsolute);
            }
            Yii::error($e, __METHOD__);
            $this->addError('nama_lengkap', 'Pendaftaran alumni belum dapat disimpan. Silakan coba kembali.');
            return null;
        }
    }

    private function savePhoto(UploadedFile $file): string
    {
        return AlumniPhotoHelper::saveNormalized($file);
    }

    private function uniqueSlug(string $name, int $batchNumber): string
    {
        $base = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
        if ($base === '') {
            $base = 'alumni';
        }
        $base .= '-batch-' . $batchNumber;
        $slug = $base;
        $counter = 2;
        while (AlumniProfile::find()->where(['slug' => $slug])->exists()) {
            $slug = $base . '-' . $counter++;
        }
        return $slug;
    }

    private function normalizeExternalUrl($value): ?string
    {
        $value = trim((string)$value);
        if ($value === '') return null;
        if (!preg_match('~^https?://~i', $value)) {
            $value = 'https://' . ltrim($value, '/');
        }
        return $value;
    }

}
