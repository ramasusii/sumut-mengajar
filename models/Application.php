<?php
namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Application extends ActiveRecord
{
    public const STATUS_DRAFT='draft';
    public const STATUS_SUBMITTED='submitted';
    public const STATUS_REVISION_REQUIRED='revision_required';
    public const STATUS_VERIFIED='verified';
    public const STATUS_ADMIN_PASS='administration_pass';
    public const STATUS_INTERVIEW='interview';
    public const STATUS_INTERVIEW_PASS='interview_pass';
    public const STATUS_FINAL_PASS='final_pass';
    public const STATUS_REJECTED='rejected';

    public static function tableName(){ return 'application'; }
    public function behaviors(){ return [TimestampBehavior::className()]; }

    public function rules()
    {
        return [
            [['application_code','batch_id','user_id','status'],'required'],
            [['batch_id','user_id','verified_by','created_at','updated_at'],'integer'],
            [['submitted_at','verified_at'],'safe'],
            [['application_code'],'string','max'=>40],
            [['status'],'string','max'=>40],
            [['application_code'],'unique'],
        ];
    }

    public function getBatch(){ return $this->hasOne(RecruitmentBatch::class,['id'=>'batch_id']); }
    public function getUser(){ return $this->hasOne(User::class,['id'=>'user_id']); }
    public function getProfile(){ return $this->hasOne(ApplicantProfile::class, ['user_id' => 'user_id']); }
    public function getAnswers(){ return $this->hasMany(ApplicationAnswer::class, ['application_id' => 'id']); }
    public function getDocuments(){ return $this->hasMany(ApplicationDocument::class, ['application_id' => 'id']); }
    public function getNotes(){ return $this->hasMany(ApplicationNote::class, ['application_id' => 'id'])->orderBy(['created_at' => SORT_DESC]); }
    public function getVerifier(){ return $this->hasOne(User::class, ['id' => 'verified_by']); }

    public static function generateCode($batchId)
    {
        return sprintf('GSM-%s-%02d-%05d', date('Y'), (int)$batchId, random_int(1,99999));
    }
}
