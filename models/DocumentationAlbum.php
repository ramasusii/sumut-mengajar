<?php
namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class DocumentationAlbum extends ActiveRecord
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    public static function tableName()
    {
        return 'documentation_album';
    }

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public function rules()
    {
        return [
            [['kabupaten_kota_id', 'title', 'year', 'status'], 'required'],
            [['kabupaten_kota_id', 'batch_id', 'year', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['description'], 'string'],
            [['published_at'], 'safe'],
            [['title'], 'string', 'max' => 180],
            [['slug'], 'string', 'max' => 190],
            [['year'], 'integer', 'min' => 2000, 'max' => 2100],
            [['status'], 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_PUBLISHED]],
            [['slug'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'kabupaten_kota_id' => 'Daerah Pengabdian',
            'batch_id' => 'Batch Terkait',
            'title' => 'Nama Album',
            'year' => 'Tahun',
            'description' => 'Deskripsi Album',
            'status' => 'Status Publikasi',
        ];
    }

    public function getRegion()
    {
        return $this->hasOne(KabupatenKota::class, ['id' => 'kabupaten_kota_id']);
    }

    public function getBatch()
    {
        return $this->hasOne(RecruitmentBatch::class, ['id' => 'batch_id']);
    }

    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getPhotos()
    {
        return $this->hasMany(DocumentationPhoto::class, ['album_id' => 'id'])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC]);
    }

    public function getCoverPhoto()
    {
        return $this->hasOne(DocumentationPhoto::class, ['album_id' => 'id'])
            ->andOnCondition(['is_cover' => 1])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC]);
    }

    public function getFirstPhoto()
    {
        return $this->hasOne(DocumentationPhoto::class, ['album_id' => 'id'])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC]);
    }

    public function getDisplayCoverPhoto()
    {
        return $this->coverPhoto ?: $this->firstPhoto;
    }

    public function getPhotoCount(): int
    {
        return (int) $this->getPhotos()->count();
    }

    public static function publishedQuery()
    {
        return static::find()
            ->where(['documentation_album.status' => self::STATUS_PUBLISHED]);
    }
}
