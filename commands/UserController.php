<?php
namespace app\commands;
use app\models\User;
use Yii;
use yii\console\Controller;
class UserController extends Controller
{
    public function actionCreateAdmin($email, $password, $nama='Administrator')
    {
        if(User::find()->where(['email'=>mb_strtolower(trim($email))])->exists()){ $this->stderr("Email sudah terdaftar.\n"); return 1; }
        $user=new User(); $user->username=mb_strtolower(trim($email)); $user->email=$user->username; $user->nama=$nama; $user->role='superAdmin'; $user->account_type='admin'; $user->status=User::STATUS_ACTIVE; $user->setPassword($password); $user->generateAuthKey();
        if(!$user->save()){ $this->stderr("Gagal membuat admin: ".json_encode($user->errors)."\n"); return 1; }
        $role=Yii::$app->authManager->getRole('superAdmin'); if($role) Yii::$app->authManager->assign($role,$user->id);
        $this->stdout("Admin berhasil dibuat: {$user->email}\n"); return 0;
    }
}
