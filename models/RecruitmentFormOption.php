<?php
namespace app\models;

use yii\db\ActiveRecord;

class RecruitmentFormOption extends ActiveRecord
{
    public static function tableName()
    {
        return 'recruitment_form_option';
    }
}
