<?php
namespace app\models;
use yii\db\ActiveRecord;
class Chapter extends ActiveRecord
{
    public static function tableName(){ return 'chapter'; }
    public function rules(){ return [[['name','slug','kabupaten_kota_id'],'required'],[['kabupaten_kota_id','is_active'],'integer'],[['name'],'string','max'=>150],[['slug'],'string','max'=>160],[['description'],'string'],[['slug'],'unique']]; }
    public function getKabupatenKota(){ return $this->hasOne(KabupatenKota::class,['id'=>'kabupaten_kota_id']); }
}
