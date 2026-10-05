<?php
namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class AlumniProfile extends ActiveRecord
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_REJECTED = 'rejected';

    public static function tableName(){ return 'alumni_profile'; }
    public function behaviors(){ return [TimestampBehavior::class]; }

    public function rules()
    {
        return [
            [['nama_lengkap', 'batch_number', 'whatsapp'], 'required'],
            [['user_id', 'application_id', 'batch_id', 'batch_number', 'batch_year', 'is_public', 'is_featured', 'consent_public', 'verified_by', 'created_at', 'updated_at'], 'integer'],
            [['bio'], 'string'],
            [['verified_at'], 'safe'],
            [['nama_lengkap', 'location_name', 'current_position', 'current_institution', 'sector', 'work_city'], 'string', 'max' => 180],
            [['whatsapp'], 'string', 'max' => 30],
            [['slug'], 'string', 'max' => 190],
            [['photo', 'linkedin', 'instagram'], 'string', 'max' => 500],
            [['linkedin'], 'validateExternalUrl', 'skipOnEmpty' => true],
            [['verification_status'], 'in', 'range' => [self::STATUS_PENDING, self::STATUS_VERIFIED, self::STATUS_REJECTED]],
            [['is_public'], 'validatePublicationConsent'],
            [['is_featured'], 'validateFeatured'],
            [['slug'], 'unique'],
            [['whatsapp'], 'unique'],
        ];
    }

    public function getUser(){ return $this->hasOne(User::class, ['id' => 'user_id']); }
    public function getApplication(){ return $this->hasOne(Application::class, ['id' => 'application_id']); }
    public function getBatch(){ return $this->hasOne(RecruitmentBatch::class, ['id' => 'batch_id']); }
    public function getCareers(){ return $this->hasMany(AlumniCareer::class, ['alumni_id' => 'id'])->orderBy(['is_current' => SORT_DESC, 'start_year' => SORT_DESC, 'id' => SORT_DESC]); }
    public function getPublications(){ return $this->hasMany(AlumniPublication::class, ['alumni_id' => 'id'])->where(['is_public' => 1])->orderBy(['publication_year' => SORT_DESC, 'id' => SORT_DESC]); }
    public function getAllPublications(){ return $this->hasMany(AlumniPublication::class, ['alumni_id' => 'id'])->orderBy(['publication_year' => SORT_DESC, 'id' => SORT_DESC]); }

    public static function publicQuery()
    {
        return static::find()->where([
            'verification_status' => self::STATUS_VERIFIED,
            'is_public' => 1,
            'consent_public' => 1,
        ]);
    }

    public function validateExternalUrl($attribute): void
    {
        $value = trim((string)$this->$attribute);
        if ($value === '') return;
        if (!preg_match('~^https?://~i', $value)) {
            $value = 'https://' . ltrim($value, '/');
        }
        if (!filter_var($value, FILTER_VALIDATE_URL) || !in_array(strtolower((string)parse_url($value, PHP_URL_SCHEME)), ['http','https'], true)) {
            $this->addError($attribute, 'Masukkan tautan http/https yang valid.');
            return;
        }
        $this->$attribute = $value;
    }


    public function validatePublicationConsent($attribute): void
    {
        if ((int)$this->is_public === 1 && (int)$this->consent_public !== 1) {
            $this->addError($attribute, 'Profil hanya dapat dipublikasikan setelah persetujuan alumni tercatat.');
        }
        if ((int)$this->is_public === 1 && $this->verification_status !== self::STATUS_VERIFIED) {
            $this->addError($attribute, 'Profil hanya dapat dipublikasikan setelah status alumni terverifikasi.');
        }
    }

    public function validateFeatured($attribute): void
    {
        if ((int)$this->is_featured === 1 && (int)$this->is_public !== 1) {
            $this->addError($attribute, 'Alumni unggulan harus dipublikasikan terlebih dahulu.');
        }
    }

}
