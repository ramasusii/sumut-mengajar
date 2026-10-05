<?php
namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class ApplicationAnswer extends ActiveRecord
{
    public static function tableName()
    {
        return 'application_answer';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            [['application_id', 'field_id'], 'required'],
            [['application_id', 'field_id', 'created_at', 'updated_at'], 'integer'],
            [['answer_text', 'answer_json'], 'string'],
        ];
    }

    public function getField()
    {
        return $this->hasOne(RecruitmentFormField::class, ['id' => 'field_id']);
    }
}
