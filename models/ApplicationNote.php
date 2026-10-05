<?php
namespace app\models;

use yii\db\ActiveRecord;

class ApplicationNote extends ActiveRecord
{
    public static function tableName()
    {
        return 'application_note';
    }

    public function rules()
    {
        return [
            [['application_id', 'user_id', 'note'], 'required'],
            [['application_id', 'user_id', 'is_internal', 'created_at'], 'integer'],
            [['note'], 'string'],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
