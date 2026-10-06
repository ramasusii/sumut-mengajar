<?php
namespace app\models;

use yii\db\ActiveRecord;

class DocumentationPhoto extends ActiveRecord
{
    public static function tableName()
    {
        return 'documentation_photo';
    }

    public function rules()
    {
        return [
            [['album_id', 'file_path'], 'required'],
            [['album_id', 'file_size', 'width', 'height', 'sort_order', 'is_cover', 'created_at'], 'integer'],
            [['caption'], 'string', 'max' => 500],
            [['file_path', 'thumb_path', 'original_name'], 'string', 'max' => 255],
            [['mime_type'], 'string', 'max' => 100],
        ];
    }

    public function getAlbum()
    {
        return $this->hasOne(DocumentationAlbum::class, ['id' => 'album_id']);
    }
}
