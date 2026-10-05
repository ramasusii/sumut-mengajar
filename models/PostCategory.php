<?php
namespace app\models;

use yii\db\ActiveRecord;

class PostCategory extends ActiveRecord
{
    public static function tableName()
    {
        return 'post_category';
    }

    public function rules()
    {
        return [
            [['name','slug'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['slug'], 'string', 'max' => 110],
            [['slug'], 'unique'],
        ];
    }

    public function getPosts()
    {
        return $this->hasMany(Post::class, ['category_id' => 'id']);
    }
}
