<?php
namespace app\models;
use yii\db\ActiveRecord;
class KabupatenKota extends ActiveRecord
{
    public static function tableName() { return 'kabupaten_kota'; }
    public function rules() { return [[['nama','jenis','slug'], 'required'], [['is_active'], 'integer'], [['nama'], 'string', 'max'=>120], [['jenis'], 'string', 'max'=>20], [['slug'], 'string', 'max'=>140], [['slug'], 'unique']]; }
    public function getLabel() { return $this->jenis . ' ' . $this->nama; }
}
