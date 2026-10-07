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

    // Legacy single-publication attributes are retained for compatibility.
    public $publication_title;
    public $publication_type;
    public $publication_year;
    public $publication_url;
    public $publication_about_service = 0;

    // V27: registration can submit multiple works/research items.
    public $publications = [];

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
            ['instagram', 'validateInstagram', 'skipOnEmpty' => true],
            ['linkedin', 'validateLinkedIn', 'skipOnEmpty' => true],
            ['publication_url', 'url', 'defaultScheme' => 'https', 'skipOnEmpty' => true],

            [['publication_title'], 'string', 'max' => 255],
            [['publication_type'], 'in', 'range' => array_keys(AlumniPublication::TYPES), 'skipOnEmpty' => true],

            ['publications', 'validatePublications'],

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

    public function validateInstagram($attribute): void
    {
        $normalized = $this->normalizeInstagram($this->$attribute);
        if ($normalized === false) {
            $this->addError($attribute, 'Masukkan username Instagram atau tautan profil Instagram yang valid.');
            return;
        }
        $this->$attribute = $normalized;
    }

    public function validateLinkedIn($attribute): void
    {
        $normalized = $this->normalizeLinkedIn($this->$attribute);
        if ($normalized === false) {
            $this->addError($attribute, 'Masukkan tautan profil LinkedIn yang valid.');
            return;
        }
        $this->$attribute = $normalized;
    }

    public function validatePublications($attribute): void
    {
        $rows = is_array($this->$attribute) ? $this->$attribute : [];

        if (count($rows) > 10) {
            $this->addError($attribute, 'Maksimal 10 karya dapat dikirim dalam satu pendaftaran.');
            $rows = array_slice($rows, 0, 10);
        }

        $clean = [];
        $currentYear = (int)date('Y');

        foreach ($rows as $index => $row) {
            if (!is_array($row)) {
                continue;
            }

            $title = trim((string)($row['title'] ?? ''));
            $type = trim((string)($row['publication_type'] ?? ''));
            $yearRaw = trim((string)($row['publication_year'] ?? ''));
            $publisher = trim((string)($row['institution_or_publisher'] ?? ''));
            $urlRaw = trim((string)($row['url'] ?? ''));
            $summary = trim((string)($row['summary'] ?? ''));
            $aboutService = !empty($row['is_about_service']) ? 1 : 0;

            $hasAnyValue = $title !== ''
                || $type !== ''
                || $yearRaw !== ''
                || $publisher !== ''
                || $urlRaw !== ''
                || $summary !== ''
                || $aboutService === 1;

            if (!$hasAnyValue) {
                continue;
            }

            $number = $index + 1;

            if ($title === '') {
                $this->addError($attribute, "Judul pada Karya {$number} wajib diisi.");
            } elseif (mb_strlen($title) > 255) {
                $this->addError($attribute, "Judul pada Karya {$number} maksimal 255 karakter.");
            }

            if ($type === '') {
                $this->addError($attribute, "Jenis pada Karya {$number} wajib dipilih.");
            } elseif (!array_key_exists($type, AlumniPublication::TYPES)) {
                $this->addError($attribute, "Jenis pada Karya {$number} tidak valid.");
            }

            $year = null;
            if ($yearRaw !== '') {
                if (!ctype_digit($yearRaw)) {
                    $this->addError($attribute, "Tahun pada Karya {$number} harus berupa angka.");
                } else {
                    $year = (int)$yearRaw;
                    if ($year < 1900 || $year > $currentYear) {
                        $this->addError($attribute, "Tahun pada Karya {$number} harus antara 1900 dan {$currentYear}.");
                    }
                }
            }

            if (mb_strlen($publisher) > 255) {
                $this->addError($attribute, "Institusi/penerbit pada Karya {$number} maksimal 255 karakter.");
            }

            if (mb_strlen($summary) > 1500) {
                $this->addError($attribute, "Ringkasan pada Karya {$number} maksimal 1.500 karakter.");
            }

            $url = null;
            if ($urlRaw !== '') {
                $url = $this->normalizeExternalUrl($urlRaw);
                if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
                    $this->addError($attribute, "Tautan pada Karya {$number} tidak valid.");
                    $url = null;
                }
            }

            $clean[] = [
                'title' => $title,
                'publication_type' => $type ?: 'other',
                'publication_year' => $year,
                'institution_or_publisher' => $publisher ?: null,
                'url' => $url,
                'summary' => $summary ?: null,
                'is_about_service' => $aboutService,
            ];
        }

        $this->$attribute = $clean;
    }

    public function submit(): ?AlumniProfile
    {
        $this->photo = UploadedFile::getInstance($this, 'photo');

        // Normalize social links before validation so both @username and full URLs work.
        if (trim((string)$this->instagram) !== '') {
            $normalizedInstagram = $this->normalizeInstagram($this->instagram);
            if ($normalizedInstagram !== false) {
                $this->instagram = $normalizedInstagram;
            }
        }

        if (trim((string)$this->linkedin) !== '') {
            $normalizedLinkedIn = $this->normalizeLinkedIn($this->linkedin);
            if ($normalizedLinkedIn !== false) {
                $this->linkedin = $normalizedLinkedIn;
            }
        }

        if (!$this->validate()) {
            return null;
        }

        $tx = Yii::$app->db->beginTransaction();
        $savedPhotoAbsolute = null;

        try {
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
                if (!$career->save()) {
                    throw new \RuntimeException('Riwayat karier belum dapat disimpan.');
                }
            }

            // V27: save every submitted work/research entry.
            foreach ((array)$this->publications as $row) {
                $publication = new AlumniPublication([
                    'alumni_id' => $profile->id,
                    'title' => $row['title'],
                    'publication_type' => $row['publication_type'],
                    'publication_year' => $row['publication_year'],
                    'institution_or_publisher' => $row['institution_or_publisher'],
                    'url' => $row['url'],
                    'summary' => $row['summary'],
                    'is_about_service' => (int)$row['is_about_service'],
                    'is_public' => 1,
                    'created_at' => time(),
                    'updated_at' => time(),
                ]);

                if (!$publication->save()) {
                    throw new \RuntimeException('Data karya alumni belum dapat disimpan.');
                }
            }

            // Compatibility: accept the old single-publication fields if an older form posts them.
            if (!$this->publications && trim((string)$this->publication_title) !== '') {
                $publication = new AlumniPublication([
                    'alumni_id' => $profile->id,
                    'title' => trim($this->publication_title),
                    'publication_type' => $this->publication_type ?: 'other',
                    'publication_year' => $this->publication_year ?: null,
                    'url' => $this->normalizeExternalUrl($this->publication_url),
                    'is_about_service' => (int)$this->publication_about_service,
                    'is_public' => 1,
                    'created_at' => time(),
                    'updated_at' => time(),
                ]);
                if (!$publication->save()) {
                    throw new \RuntimeException('Data karya alumni belum dapat disimpan.');
                }
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

    private function normalizeInstagram($value)
    {
        $value = trim((string)$value);
        if ($value === '') {
            return null;
        }

        if (preg_match('~^https?://~i', $value)) {
            $host = strtolower((string)parse_url($value, PHP_URL_HOST));
            $host = preg_replace('/^www\./', '', $host);

            if ($host !== 'instagram.com') {
                return false;
            }

            $path = trim((string)parse_url($value, PHP_URL_PATH), '/');
            $parts = array_values(array_filter(explode('/', $path)));
            $username = $parts[0] ?? '';
        } else {
            $username = ltrim($value, '@');
        }

        if (!preg_match('/^[A-Za-z0-9._]{1,30}$/', $username)) {
            return false;
        }

        return 'https://www.instagram.com/' . $username . '/';
    }

    private function normalizeLinkedIn($value)
    {
        $value = trim((string)$value);
        if ($value === '') {
            return null;
        }

        if (!preg_match('~^https?://~i', $value)) {
            $value = 'https://' . ltrim($value, '/');
        }

        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            return false;
        }

        $host = strtolower((string)parse_url($value, PHP_URL_HOST));
        $host = preg_replace('/^www\./', '', $host);

        if ($host !== 'linkedin.com' && !str_ends_with($host, '.linkedin.com')) {
            return false;
        }

        return $value;
    }

    private function normalizeExternalUrl($value): ?string
    {
        $value = trim((string)$value);
        if ($value === '') {
            return null;
        }

        if (!preg_match('~^https?://~i', $value)) {
            $value = 'https://' . ltrim($value, '/');
        }

        $scheme = strtolower((string)parse_url($value, PHP_URL_SCHEME));
        if (!in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        return $value;
    }
}
