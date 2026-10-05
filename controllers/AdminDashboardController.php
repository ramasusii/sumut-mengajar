<?php
namespace app\controllers;

use app\models\AlumniProfile;
use app\models\Application;
use app\models\HeroSlide;
use app\models\RecruitmentBatch;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class AdminDashboardController extends Controller
{
    public $layout = 'main';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['superAdmin', 'adminGsm', 'reviewer']],
                ],
                'denyCallback' => function () {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['/site/staff-login']);
                    }
                    if (Yii::$app->user->can('applicant')) {
                        return Yii::$app->response->redirect(['/applicant/dashboard']);
                    }
                    throw new ForbiddenHttpException('Akun ini tidak memiliki akses ke Portal Petugas.');
                },
            ],
        ];
    }

    public function actionIndex()
    {
        $today = date('Y-m-d');
        $isContentAdmin = Yii::$app->user->can('superAdmin') || Yii::$app->user->can('adminGsm');

        return $this->render('index', [
            'batchCount' => RecruitmentBatch::find()->count(),
            'openBatchCount' => RecruitmentBatch::find()
                ->where(['status' => RecruitmentBatch::STATUS_OPEN])
                ->andWhere(['<=', 'registration_start', $today])
                ->andWhere(['>=', 'registration_end', $today])
                ->count(),
            'applicantCount' => User::find()->where(['account_type' => 'applicant'])->count(),
            'submittedCount' => Application::find()->where(['status' => Application::STATUS_SUBMITTED])->count(),
            'revisionCount' => Application::find()->where(['status' => Application::STATUS_REVISION_REQUIRED])->count(),
            'alumniCount' => $isContentAdmin ? AlumniProfile::find()->count() : 0,
            'pendingAlumniCount' => $isContentAdmin ? AlumniProfile::find()->where(['verification_status' => AlumniProfile::STATUS_PENDING])->count() : 0,
            'heroCount' => $isContentAdmin ? HeroSlide::activeQuery()->count() : 0,
            'isContentAdmin' => $isContentAdmin,
        ]);
    }
}
