<?php
namespace app\controllers;

use app\models\AlumniProfile;
use app\models\AlumniPublication;
use app\models\AlumniRegistrationForm;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class AlumniController extends Controller
{
    public $layout = 'guest';

    public function actionIndex()
    {
        $q = trim((string)Yii::$app->request->get('q', ''));
        $batch = (int)Yii::$app->request->get('batch', 0);
        $sector = trim((string)Yii::$app->request->get('sector', ''));

        $query = AlumniProfile::publicQuery()->with(['publications']);

        if ($q !== '') {
            $query->andWhere([
                'or',
                ['like', 'nama_lengkap', $q],
                ['like', 'current_institution', $q],
                ['like', 'current_position', $q],
                ['like', 'location_name', $q],
            ]);
        }
        if ($batch > 0) {
            $query->andWhere(['batch_number' => $batch]);
        }
        if ($sector !== '') {
            $query->andWhere(['sector' => $sector]);
        }

        $pagination = new Pagination([
            'totalCount' => (clone $query)->count(),
            'pageSize' => 12,
            'pageSizeParam' => false,
        ]);

        $models = $query
            ->orderBy(['is_featured' => SORT_DESC, 'batch_number' => SORT_DESC, 'nama_lengkap' => SORT_ASC])
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $batches = AlumniProfile::publicQuery()
            ->select('batch_number')
            ->distinct()
            ->orderBy(['batch_number' => SORT_DESC])
            ->column();

        $sectors = AlumniProfile::publicQuery()
            ->andWhere(['not', ['sector' => null]])
            ->andWhere(['<>', 'sector', ''])
            ->select('sector')
            ->distinct()
            ->orderBy(['sector' => SORT_ASC])
            ->column();

        $stats = [
            'alumni' => (int) AlumniProfile::publicQuery()->count(),
            'locations' => count(
                AlumniProfile::publicQuery()
                    ->andWhere(['not', ['location_name' => null]])
                    ->andWhere(['<>', 'location_name', ''])
                    ->select('location_name')
                    ->distinct()
                    ->column()
            ),
            'batches' => count(
                AlumniProfile::publicQuery()
                    ->select('batch_number')
                    ->distinct()
                    ->column()
            ),
            'sectors' => count(
                AlumniProfile::publicQuery()
                    ->andWhere(['not', ['sector' => null]])
                    ->andWhere(['<>', 'sector', ''])
                    ->select('sector')
                    ->distinct()
                    ->column()
            ),
            // Tetap dipertahankan untuk kompatibilitas halaman/detail lama.
            'institutions' => count(
                AlumniProfile::publicQuery()
                    ->andWhere(['not', ['current_institution' => null]])
                    ->andWhere(['<>', 'current_institution', ''])
                    ->select('current_institution')
                    ->distinct()
                    ->column()
            ),
            'works' => (int) AlumniPublication::find()
                ->alias('p')
                ->innerJoin('alumni_profile a', 'a.id = p.alumni_id')
                ->where([
                    'p.is_public' => 1,
                    'a.is_public' => 1,
                    'a.consent_public' => 1,
                    'a.verification_status' => AlumniProfile::STATUS_VERIFIED,
                ])
                ->count(),
        ];

        return $this->render('index', compact('models', 'pagination', 'batches', 'sectors', 'stats', 'q', 'batch', 'sector'));
    }

    public function actionView($slug)
    {
        $model = AlumniProfile::publicQuery()
            ->andWhere(['slug' => $slug])
            ->with(['careers', 'publications'])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('Profil alumni tidak ditemukan.');
        }

        return $this->render('view', ['model' => $model]);
    }

    public function actionRegister()
    {
        $model = new AlumniRegistrationForm();
        if ($model->load(Yii::$app->request->post()) && ($profile = $model->submit())) {
            Yii::$app->session->setFlash('success', 'Data alumni berhasil dikirim. Tim Sumut Mengajar akan memverifikasi data sebelum profil ditampilkan di website.');
            return $this->refresh();
        }

        return $this->render('register', ['model' => $model]);
    }
}
