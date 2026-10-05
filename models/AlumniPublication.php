<?php
namespace app\models;

use yii\db\ActiveRecord;

class AlumniPublication extends ActiveRecord
{
    public const TYPES = [
        'article' => 'Artikel',
        'book' => 'Buku',
        'journal' => 'Jurnal',
        'thesis' => 'Skripsi',
        'master_thesis' => 'Tesis',
        'research' => 'Penelitian',
        'report' => 'Laporan Pengabdian',
        'essay' => 'Esai',
        'other' => 'Karya Lainnya',
    ];

    public static function tableName(){ return 'alumni_publication'; }

    public function rules()
    {
        return [
            [['alumni_id', 'title', 'publication_type'], 'required'],
            [['alumni_id', 'publication_year', 'is_about_service', 'is_public', 'created_at', 'updated_at'], 'integer'],
            [['summary'], 'string'],
            [['title', 'institution_or_publisher'], 'string', 'max' => 255],
            [['publication_type'], 'in', 'range' => array_keys(self::TYPES)],
            [['url'], 'string', 'max' => 500],
            [['url'], 'validateExternalUrl', 'skipOnEmpty' => true],
        ];
    }

    public function getAlumni(){ return $this->hasOne(AlumniProfile::class, ['id' => 'alumni_id']); }

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

}
