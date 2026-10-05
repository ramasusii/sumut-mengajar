<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class ApplicationDocument extends ActiveRecord
{
    public static function tableName(){ return 'application_document'; }

    public function rules()
    {
        return [
            [['application_id', 'file_path'], 'required'],
            [['application_id', 'field_id', 'file_size', 'verified_by', 'created_at'], 'integer'],
            [['verified_at'], 'safe'],
            [['file_path'], 'string', 'max' => 500],
            [['original_name'], 'string', 'max' => 255],
            [['mime_type'], 'string', 'max' => 120],
            [['document_type'], 'string', 'max' => 80],
            [['verification_note'], 'string', 'max' => 500],
        ];
    }

    public function getField(){ return $this->hasOne(RecruitmentFormField::class, ['id' => 'field_id']); }

    public function getAbsolutePath(): ?string
    {
        $path = ltrim((string)$this->file_path, '/');
        $candidates = [];

        if (str_starts_with($path, 'private/')) {
            $candidates[] = Yii::getAlias('@app/storage/' . $path);
        }

        $candidates[] = Yii::getAlias('@app/' . $path);
        $candidates[] = Yii::getAlias('@webroot/' . $path);

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
