<?php
namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class HeroSlide extends ActiveRecord
{
    public static function tableName(){ return 'hero_slide'; }
    public function behaviors(){ return [TimestampBehavior::class]; }

    public function rules()
    {
        return [
            [['title'], 'required'],
            [['subtitle'], 'string'],
            [['sort_order', 'is_published', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['start_at', 'end_at'], 'safe'],
            [['title'], 'string', 'max' => 180],
            [['badge'], 'string', 'max' => 80],
            [['desktop_image', 'mobile_image', 'primary_url', 'secondary_url'], 'string', 'max' => 500],
            [['primary_label', 'secondary_label'], 'string', 'max' => 80],
            [['primary_url', 'secondary_url'], 'validateSafeLink'],
            [['end_at'], 'validateSchedule'],
        ];
    }

    public static function activeQuery()
    {
        $now = date('Y-m-d H:i:s');
        return static::find()
            ->where(['is_published' => 1])
            ->andWhere(['or', ['start_at' => null], ['<=', 'start_at', $now]])
            ->andWhere(['or', ['end_at' => null], ['>=', 'end_at', $now]])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_DESC]);
    }

    public function validateSafeLink($attribute): void
    {
        $value = trim((string)$this->$attribute);
        if ($value === '') {
            return;
        }

        if (str_starts_with($value, '/')) {
            $this->$attribute = $value;
            return;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            $scheme = strtolower((string)parse_url($value, PHP_URL_SCHEME));
            if (in_array($scheme, ['http', 'https'], true)) {
                $this->$attribute = $value;
                return;
            }
        }

        $this->addError($attribute, 'Gunakan URL internal yang diawali / atau URL http/https yang valid.');
    }


    public function validateSchedule($attribute): void
    {
        if (!$this->start_at || !$this->end_at) return;
        if (strtotime((string)$this->end_at) < strtotime((string)$this->start_at)) {
            $this->addError($attribute, 'Waktu selesai tayang tidak boleh sebelum waktu mulai.');
        }
    }

}
