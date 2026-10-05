<?php
namespace app\models;

use yii\db\ActiveRecord;

class AlumniCareer extends ActiveRecord
{
    public static function tableName(){ return 'alumni_career'; }

    public function rules()
    {
        return [
            [['alumni_id', 'institution_name', 'position_title'], 'required'],
            [['alumni_id', 'start_year', 'end_year', 'is_current', 'is_public'], 'integer'],
            [['institution_name', 'position_title', 'sector', 'city'], 'string', 'max' => 180],
        ];
    }

    public function getAlumni(){ return $this->hasOne(AlumniProfile::class, ['id' => 'alumni_id']); }
}
