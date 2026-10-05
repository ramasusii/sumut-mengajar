<?php
namespace app\controllers;

use app\models\AlumniProfile;
use app\models\HeroSlide;
use app\models\LoginForm;
use app\models\PublicTrackingForm;
use app\models\RecruitmentBatch;
use app\models\RegisterForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class SiteController extends Controller
{
    public $layout = 'guest';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    ['actions' => ['logout'], 'allow' => true, 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['logout' => ['post']],
            ],
        ];
    }

    public function actionIndex()
    {
        $today = date('Y-m-d');

        $activeBatch = RecruitmentBatch::find()
            ->where(['status' => RecruitmentBatch::STATUS_OPEN])
            ->andWhere(['<=', 'registration_start', $today])
            ->andWhere(['>=', 'registration_end', $today])
            ->orderBy(['registration_start' => SORT_DESC])
            ->one();

        $latestBatches = RecruitmentBatch::find()
            ->where(['in', 'status', [
                RecruitmentBatch::STATUS_OPEN,
                RecruitmentBatch::STATUS_CLOSED,
                RecruitmentBatch::STATUS_ANNOUNCED,
            ]])
            ->orderBy(['id' => SORT_DESC])
            ->limit(3)
            ->all();

        $heroSlides = HeroSlide::activeQuery()->all();

        $featuredAlumni = AlumniProfile::publicQuery()
            ->andWhere(['is_featured' => 1])
            ->with(['publications'])
            ->orderBy(['updated_at' => SORT_DESC])
            ->limit(4)
            ->all();

        if (!$featuredAlumni) {
            $featuredAlumni = AlumniProfile::publicQuery()
                ->with(['publications'])
                ->orderBy(['updated_at' => SORT_DESC])
                ->limit(4)
                ->all();
        }

        $alumniStats = [
            'total' => (int)AlumniProfile::publicQuery()->count(),
            'institutions' => count(AlumniProfile::publicQuery()
                ->andWhere(['not', ['current_institution' => null]])
                ->andWhere(['<>', 'current_institution', ''])
                ->select('current_institution')
                ->distinct()
                ->column()),
        ];

        return $this->render('index', [
            'activeBatch' => $activeBatch,
            'latestBatches' => $latestBatches,
            'heroSlides' => $heroSlides,
            'featuredAlumni' => $featuredAlumni,
            'alumniStats' => $alumniStats,
        ]);
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            if ($this->isStaff()) {
                return $this->redirect(['/admin-dashboard/index']);
            }

            if (Yii::$app->user->can('applicant')) {
                return $this->redirect(['/applicant/dashboard']);
            }

            Yii::$app->user->logout(false);
            Yii::$app->session->regenerateID(true);
        }

        $model = new LoginForm();

        if (
            $model->load(Yii::$app->request->post())
            && $model->loginForRoles(['applicant'], 'Portal Peserta', 'whatsapp')
        ) {
            return $this->redirect(['/applicant/dashboard']);
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionStaffLogin()
    {
        /*
         * Bug yang sering terjadi sebelumnya:
         * browser masih punya sesi peserta, lalu membuka /petugas/login.
         * Controller langsung mengirim kembali ke dashboard peserta sehingga
         * form verifikator/admin tidak pernah benar-benar bisa dipakai.
         *
         * Sekarang sesi peserta ditutup saat pengguna sengaja membuka
         * halaman login petugas. Sesi staf yang memang sudah aktif tetap
         * diarahkan ke Dashboard Petugas.
         */
        if (!Yii::$app->user->isGuest) {
            if ($this->isStaff()) {
                return $this->redirect(['/admin-dashboard/index']);
            }

            if (Yii::$app->user->can('applicant')) {
                Yii::$app->user->logout(false);
                Yii::$app->session->regenerateID(true);
                Yii::$app->session->setFlash(
                    'info',
                    'Sesi peserta sebelumnya sudah ditutup. Silakan masuk sebagai petugas.'
                );
            } else {
                Yii::$app->user->logout(false);
                Yii::$app->session->regenerateID(true);
            }
        }

        $model = new LoginForm();

        if (
            $model->load(Yii::$app->request->post())
            && $model->loginForRoles(
                ['developer', 'superAdmin', 'adminGsm', 'reviewer'],
                'Portal Petugas',
                'staff'
            )
        ) {
            return $this->redirect(['/admin-dashboard/index']);
        }

        $model->password = '';

        return $this->render('staff-login', [
            'model' => $model,
        ]);
    }

    // CEK-STATUS-V12
    public function actionTrack()
    {
        $model = new PublicTrackingForm();
        $application = null;

        /*
         * Form utama memakai parameter "kode" agar URL dan tampilan lebih
         * sederhana. Tetap menerima format form lama supaya kompatibel.
         */
        $code = trim((string)Yii::$app->request->get('kode', ''));

        if ($code === '' && Yii::$app->request->isPost) {
            $legacy = Yii::$app->request->post('PublicTrackingForm', []);
            $code = trim((string)($legacy['application_code'] ?? ''));
        }

        if ($code !== '') {
            $model->application_code = $code;

            if ($model->validate()) {
                $application = $model->getApplication();
            }
        }

        return $this->render('track', [
            'model' => $model,
            'application' => $application,
        ]);
    }

    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirectAfterLogin();
        }

        $model = new RegisterForm();
        if ($model->load(Yii::$app->request->post()) && ($user = $model->register())) {
            Yii::$app->user->login($user, 3600 * 24 * 30);
            Yii::$app->session->setFlash('success', 'Akun berhasil dibuat. Selamat datang di Portal Peserta Sumut Mengajar.');
            return $this->redirect(['/applicant/dashboard']);
        }

        return $this->render('register', ['model' => $model]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        return $this->render('error', ['exception' => $exception]);
    }

    private function redirectAfterLogin()
    {
        if ($this->isStaff()) {
            return $this->redirect(['/admin-dashboard/index']);
        }

        if (Yii::$app->user->can('applicant')) {
            return $this->redirect(['/applicant/dashboard']);
        }

        Yii::$app->user->logout();
        Yii::$app->session->setFlash('error', 'Akun ini belum memiliki akses ke halaman tersebut. Silakan hubungi pengelola Sumut Mengajar.');
        return $this->redirect(['/site/login']);
    }

    private function isStaff(): bool
    {
        return Yii::$app->user->can('developer')
            || Yii::$app->user->can('superAdmin')
            || Yii::$app->user->can('adminGsm')
            || Yii::$app->user->can('reviewer');
    }
}
