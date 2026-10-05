<?php
namespace app\commands;

use app\models\User;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class StaffController extends Controller
{
    private array $accounts = [
        [
            'username' => 'superadmin',
            'email' => 'superadmin@sumutmengajar.org',
            'nama' => 'Super Admin Sumut Mengajar',
            'role' => 'superAdmin',
            'password' => 'GSM!Super-2026',
        ],
        [
            'username' => 'admin.konten1',
            'email' => 'admin1@sumutmengajar.org',
            'nama' => 'Admin Konten 1',
            'role' => 'adminGsm',
            'password' => 'GSM!Konten1-2026',
        ],
        [
            'username' => 'admin.konten2',
            'email' => 'admin2@sumutmengajar.org',
            'nama' => 'Admin Konten 2',
            'role' => 'adminGsm',
            'password' => 'GSM!Konten2-2026',
        ],
        [
            'username' => 'verifikator2',
            'email' => 'verifikator2@sumutmengajar.org',
            'nama' => 'Verifikator Sumut Mengajar 2',
            'role' => 'reviewer',
            'password' => 'GSM!Verif2-2026',
        ],
    ];

    /**
     * Buat 4 akun staf default.
     *
     * php yii staff/seed
     * php yii staff/seed 1   # reset password jika akun sudah ada
     */
    public function actionSeed($resetPassword = 0)
    {
        $resetPassword = (bool)$resetPassword;
        $auth = Yii::$app->authManager;

        foreach ($this->accounts as $data) {
            $role = $auth->getRole($data['role']);
            if (!$role) {
                $this->stderr("ROLE TIDAK ADA: {$data['role']}. Import database/staff-rbac-v4.sql terlebih dahulu.\n");
                return ExitCode::UNSPECIFIED_ERROR;
            }

            $user = User::find()
                ->where(['username' => $data['username']])
                ->orWhere(['email' => $data['email']])
                ->one();

            $created = false;

            if (!$user) {
                $user = new User();
                $user->username = $data['username'];
                $user->email = $data['email'];
                $user->nama = $data['nama'];
                $user->role = $data['role'];
                $user->account_type = 'staff';
                $user->status = User::STATUS_ACTIVE;
                $user->whatsapp = null;
                $user->generateAuthKey();
                $user->setPassword($data['password']);

                if (!$user->save()) {
                    $this->stderr("GAGAL membuat {$data['username']}:\n");
                    foreach ($user->getFirstErrors() as $error) {
                        $this->stderr("- {$error}\n");
                    }
                    continue;
                }

                $created = true;
            } else {
                $user->nama = $data['nama'];
                $user->role = $data['role'];
                $user->account_type = 'staff';
                $user->status = User::STATUS_ACTIVE;

                if ($resetPassword) {
                    $user->setPassword($data['password']);
                }

                $user->save(false);
            }

            // Staf hanya memegang satu role operasional utama.
            foreach (['developer','superAdmin','adminGsm','reviewer','applicant'] as $roleName) {
                $existingRole = $auth->getRole($roleName);
                if ($existingRole) {
                    $auth->revoke($existingRole, $user->id);
                }
            }

            $auth->assign($role, $user->id);

            $this->stdout(
                ($created ? 'CREATED' : 'UPDATED')
                . " {$data['username']} | {$data['role']}"
                . ($created || $resetPassword ? " | password: {$data['password']}" : " | password tidak diubah")
                . "\n"
            );
        }

        $this->stdout("\nSelesai. Login staf: /petugas/login\n");
        $this->stdout("Untuk produksi, ganti password default setelah pengujian.\n");

        return ExitCode::OK;
    }

    /**
     * Jadikan satu akun staf sebagai developer.
     *
     * Contoh:
     * php yii staff/make-developer nama.username
     */
    public function actionMakeDeveloper($identifier)
    {
        $user = User::find()
            ->where(['username' => $identifier])
            ->orWhere(['email' => $identifier])
            ->one();

        if (!$user) {
            $this->stderr("Akun tidak ditemukan: {$identifier}\n");
            return ExitCode::DATAERR;
        }

        $auth = Yii::$app->authManager;
        $developer = $auth->getRole('developer');

        if (!$developer) {
            $this->stderr("Role developer belum tersedia. Import database/staff-rbac-v4.sql.\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        foreach (['developer','superAdmin','adminGsm','reviewer','applicant'] as $roleName) {
            if ($role = $auth->getRole($roleName)) {
                $auth->revoke($role, $user->id);
            }
        }

        $auth->assign($developer, $user->id);
        $user->role = 'developer';
        $user->account_type = 'staff';
        $user->save(false, ['role','account_type','updated_at']);

        $this->stdout("OK: {$user->username} sekarang role developer (akses seluruh sistem).\n");

        return ExitCode::OK;
    }
}
