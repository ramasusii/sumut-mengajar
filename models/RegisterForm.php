<?php
namespace app\models;

use app\components\PhoneHelper;
use Yii;
use yii\base\Model;

class RegisterForm extends Model
{
    public $nama;
    public $whatsapp;
    public $password;
    public $password_repeat;

    public function rules()
    {
        return [
            [['nama', 'whatsapp', 'password', 'password_repeat'], 'required'],
            ['nama', 'string', 'min' => 3, 'max' => 150],
            ['whatsapp', 'trim'],
            ['whatsapp', 'validateWhatsapp'],
            ['password', 'string', 'min' => 8],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => 'Konfirmasi password tidak sama.'],
        ];
    }

    public function validateWhatsapp($attribute)
    {
        $normalized = PhoneHelper::normalizeIndonesia($this->$attribute);
        if (!$normalized) {
            $this->addError($attribute, 'Masukkan nomor WhatsApp Indonesia yang valid. Contoh: 081234567890.');
            return;
        }

        $this->$attribute = $normalized;

        if (User::find()->where(['or', ['whatsapp' => $normalized], ['username' => $normalized]])->exists()) {
            $this->addError($attribute, 'Nomor WhatsApp sudah terdaftar. Silakan masuk ke akunmu.');
            return;
        }

        $variants = PhoneHelper::variants($normalized);
        if ($variants && ApplicantProfile::find()->where(['in', 'nomor_whatsapp', $variants])->exists()) {
            $this->addError($attribute, 'Nomor WhatsApp sudah terdaftar. Silakan masuk ke akunmu.');
        }
    }

    public function register()
    {
        if (!$this->validate()) {
            return null;
        }

        $tx = Yii::$app->db->beginTransaction();
        try {
            $user = new User();
            $user->username = $this->whatsapp;
            $user->whatsapp = $this->whatsapp;
            $user->email = null;
            $user->nama = trim($this->nama);
            $user->role = 'applicant';
            $user->account_type = 'applicant';
            $user->status = User::STATUS_ACTIVE;
            $user->setPassword($this->password);
            $user->generateAuthKey();

            if (!$user->save()) {
                throw new \RuntimeException('Gagal menyimpan akun.');
            }

            $role = Yii::$app->authManager->getRole('applicant');
            if ($role) {
                Yii::$app->authManager->assign($role, $user->id);
            }

            $profile = new ApplicantProfile([
                'user_id' => $user->id,
                'nama_lengkap' => $user->nama,
                'nomor_whatsapp' => $this->whatsapp,
            ]);

            if (!$profile->save(false)) {
                throw new \RuntimeException('Gagal membuat profil pendaftar.');
            }

            $tx->commit();
            return $user;
        } catch (\Throwable $e) {
            $tx->rollBack();
            Yii::error($e, __METHOD__);
            return null;
        }
    }
}
