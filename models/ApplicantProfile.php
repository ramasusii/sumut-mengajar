<?php
namespace app\models;

use yii\db\ActiveRecord;

class ApplicantProfile extends ActiveRecord
{
    public static function tableName()
    {
        return 'applicant_profile';
    }

    public function rules()
    {
        return [
            [['user_id', 'nama_lengkap'], 'required'],
            [['user_id', 'kabupaten_kota_id'], 'integer'],
            [['tanggal_lahir', 'profile_completed_at'], 'safe'],
            [['alamat_domisili'], 'string'],
            [['nama_lengkap'], 'string', 'max' => 150],
            [['jenis_kelamin', 'agama'], 'string', 'max' => 30],
            [['tempat_lahir', 'asal_instansi', 'pekerjaan', 'kabupaten_kota_domisili'], 'string', 'max' => 150],
            [['provinsi_domisili'], 'string', 'max' => 100],
            [['nomor_whatsapp'], 'string', 'max' => 30],
            [['pendidikan_terakhir'], 'in', 'range' => ['S1', 'D4']],
            [['instagram', 'tiktok'], 'string', 'max' => 100],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getKabupatenKota()
    {
        return $this->hasOne(KabupatenKota::class, ['id' => 'kabupaten_kota_id']);
    }

    public function getDomisiliLabel(): string
    {
        $parts = array_filter([$this->kabupaten_kota_domisili, $this->provinsi_domisili]);
        if ($parts) {
            return implode(', ', $parts);
        }
        return $this->kabupatenKota ? $this->kabupatenKota->label : '-';
    }
}
