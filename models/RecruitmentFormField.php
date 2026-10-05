<?php
namespace app\models;

use yii\db\ActiveRecord;

class RecruitmentFormField extends ActiveRecord
{
    public static function tableName()
    {
        return 'recruitment_form_field';
    }

    public function rules()
    {
        return [
            [['batch_id', 'label', 'field_key', 'field_type'], 'required'],
            [['batch_id', 'is_required', 'sort_order', 'is_active'], 'integer'],
            [['validation_json'], 'string'],
            [['section'], 'string', 'max' => 100],
            [['label', 'placeholder'], 'string', 'max' => 255],
            [['field_key'], 'string', 'max' => 100],
            [['help_text'], 'string', 'max' => 500],
        ];
    }

    public function getOptions()
    {
        return $this->hasMany(RecruitmentFormOption::class, ['field_id' => 'id'])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC]);
    }

    public function isFileField()
    {
        return in_array($this->field_type, ['file', 'multi_file'], true);
    }
}
