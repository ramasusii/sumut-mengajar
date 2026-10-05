<?php
namespace app\models;

use app\components\PhoneHelper;
use mdm\admin\components\Configs;
use mdm\admin\components\UserStatus;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE = 10;

    public static function tableName() { return Configs::instance()->userTable; }
    public function behaviors() { return [TimestampBehavior::className()]; }

    public function rules()
    {
        return [
            [['username', 'auth_key', 'password_hash'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'role', 'account_type'], 'string', 'max' => 64],
            [['email', 'password_hash', 'password_reset_token', 'foto'], 'string', 'max' => 255],
            [['whatsapp'], 'string', 'max' => 30],
            [['nama'], 'string', 'max' => 150],
            ['email', 'email'],
            [['username'], 'unique'],
            [['email'], 'unique', 'skipOnEmpty' => true],
            [['whatsapp'], 'unique', 'skipOnEmpty' => true],
            ['status', 'in', 'range' => [UserStatus::ACTIVE, UserStatus::INACTIVE]],
        ];
    }

    public static function findIdentity($id) { return static::findOne(['id' => $id, 'status' => UserStatus::ACTIVE]); }
    public static function findIdentityByAccessToken($token, $type = null) { return null; }
    public static function findByUsername($username) { return static::findOne(['username' => trim((string)$username), 'status' => UserStatus::ACTIVE]); }
    public static function findByEmail($email) { return static::findOne(['email' => mb_strtolower(trim((string)$email)), 'status' => UserStatus::ACTIVE]); }

    public static function findByWhatsapp($whatsapp)
    {
        $normalized = PhoneHelper::normalizeIndonesia((string)$whatsapp);
        if (!$normalized) {
            return null;
        }

        $user = static::find()
            ->where(['status' => UserStatus::ACTIVE])
            ->andWhere(['or', ['whatsapp' => $normalized], ['username' => $normalized]])
            ->one();

        if ($user) {
            return $user;
        }

        $variants = PhoneHelper::variants($normalized);
        if (!$variants) {
            return null;
        }

        return static::find()
            ->alias('u')
            ->joinWith(['applicantProfile p'])
            ->where(['u.status' => UserStatus::ACTIVE])
            ->andWhere(['in', 'p.nomor_whatsapp', $variants])
            ->one();
    }

    public function getId() { return $this->getPrimaryKey(); }
    public function getAuthKey() { return $this->auth_key; }
    public function validateAuthKey($authKey) { return $this->auth_key === $authKey; }
    public function validatePassword($password) { return Yii::$app->security->validatePassword($password, $this->password_hash); }
    public function setPassword($password) { $this->password_hash = Yii::$app->security->generatePasswordHash($password); }
    public function generateAuthKey() { $this->auth_key = Yii::$app->security->generateRandomString(); }
    public static function getDb() { return Configs::userDb(); }
    public function getRoles() { return Yii::$app->authManager->getRolesByUser($this->id); }
    public function getApplicantProfile() { return $this->hasOne(ApplicantProfile::class, ['user_id' => 'id']); }
    public function getAlumniProfile() { return $this->hasOne(AlumniProfile::class, ['user_id' => 'id']); }
}
