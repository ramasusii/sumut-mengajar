<?php
namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Post extends ActiveRecord
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    public static function tableName()
    {
        return 'post';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            [['title','slug','status'], 'required'],
            [['category_id','created_by','created_at','updated_at'], 'integer'],
            [['content'], 'string'],
            [['published_at'], 'safe'],
            [['title'], 'string', 'max' => 220],
            [['slug'], 'string', 'max' => 230],
            [['excerpt'], 'string', 'max' => 600],
            [['cover_image'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => [self::STATUS_DRAFT,self::STATUS_PUBLISHED,self::STATUS_ARCHIVED]],
            [['slug'], 'unique'],
        ];
    }

    public function getCategory()
    {
        return $this->hasOne(PostCategory::class, ['id' => 'category_id']);
    }

    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public static function publishedQuery()
    {
        return static::find()
            ->where(['status' => self::STATUS_PUBLISHED])
            ->andWhere(['or', ['published_at' => null], ['<=', 'published_at', date('Y-m-d H:i:s')]]);
    }
}
