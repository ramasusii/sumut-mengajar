<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\Query;

class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;
    private string $loginMode = 'any';

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if ($this->hasErrors()) {
            return;
        }

        if ($this->tooManyAttempts()) {
            $this->addError($attribute, 'Terlalu banyak percobaan masuk. Tunggu 15 menit, lalu coba kembali.');
            return;
        }

        $user = $this->getUser();
        if (!$user || !$user->validatePassword($this->password)) {
            $this->recordAttempt(false, 'Kredensial tidak sesuai');
            $message = $this->loginMode === 'whatsapp'
                ? 'Nomor WhatsApp atau password tidak sesuai.'
                : 'Email/username atau password tidak sesuai.';
            $this->addError($attribute, $message);
        }
    }

    public function login()
    {
        return $this->loginForRoles([]);
    }

    public function loginForRoles(array $allowedRoles, string $portalLabel = 'portal ini', string $mode = 'any'): bool
    {
        $this->loginMode = $mode;
        $this->_user = false;

        if (!$this->validate()) {
            return false;
        }

        $user = $this->getUser();
        $assignedRoles = array_keys(Yii::$app->authManager->getRolesByUser($user->id));

        if ($allowedRoles && !array_intersect($allowedRoles, $assignedRoles)) {
            $this->addError('username', 'Akun ini tidak memiliki akses ke ' . $portalLabel . '. Gunakan halaman masuk yang sesuai.');
            return false;
        }

        $loggedIn = Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);

        if ($loggedIn) {
            $user->last_login_at = date('Y-m-d H:i:s');
            $user->save(false, ['last_login_at', 'updated_at']);
            $this->recordAttempt(true, null);
        }

        return $loggedIn;
    }

    public function getUser()
    {
        if ($this->_user !== false) {
            return $this->_user;
        }

        $identifier = trim((string)$this->username);

        if ($this->loginMode === 'whatsapp') {
            $this->_user = User::findByWhatsapp($identifier);
            return $this->_user;
        }

        if ($this->loginMode === 'staff') {
            $this->_user = User::findByUsername($identifier) ?: User::findByEmail($identifier);
            return $this->_user;
        }

        $this->_user = User::findByWhatsapp($identifier)
            ?: User::findByUsername($identifier)
            ?: User::findByEmail($identifier);

        return $this->_user;
    }

    private function attemptKey(): string
    {
        return mb_strtolower(trim((string)$this->username));
    }

    private function tooManyAttempts(): bool
    {
        try {
            return (int)(new Query())
                ->from('login_attempt')
                ->where([
                    'username' => $this->attemptKey(),
                    'ip_address' => $this->clientIp(),
                    'successful' => 0,
                ])
                ->andWhere(['>=', 'attempted_at', date('Y-m-d H:i:s', time() - 900)])
                ->count() >= 5;
        } catch (\Throwable $e) {
            Yii::warning($e, __METHOD__);
            return false;
        }
    }

    private function recordAttempt(bool $successful, ?string $reason): void
    {
        try {
            Yii::$app->db->createCommand()->insert('login_attempt', [
                'username' => $this->attemptKey(),
                'ip_address' => $this->clientIp(),
                'successful' => $successful ? 1 : 0,
                'reason' => $reason,
                'attempted_at' => date('Y-m-d H:i:s'),
            ])->execute();
        } catch (\Throwable $e) {
            Yii::warning($e, __METHOD__);
        }
    }

    private function clientIp(): string
    {
        return mb_strimwidth((string)(Yii::$app->request->userIP ?: 'unknown'), 0, 45, '');
    }
}
