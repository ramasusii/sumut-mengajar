<?php
namespace app\controllers;

use app\models\KabupatenKota;
use app\models\RecruitmentBatch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class AdminRecruitmentController extends Controller
{
    public $layout='main';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['superAdmin', 'adminGsm']],
                ],
                'denyCallback' => function () {
                    if (Yii::$app->user->isGuest) {
                        Yii::$app->user->setReturnUrl(Yii::$app->request->url);
                        return Yii::$app->response->redirect(['/site/staff-login']);
                    }
                    if (Yii::$app->user->can('reviewer')) {
                        return Yii::$app->response->redirect(['/admin-dashboard/index']);
                    }
                    if (Yii::$app->user->can('applicant')) {
                        return Yii::$app->response->redirect(['/applicant/dashboard']);
                    }
                    throw new ForbiddenHttpException('Akun ini tidak memiliki akses untuk mengelola batch.');
                },
            ],
        ];
    }

    public function actionIndex()
    {
        $models = RecruitmentBatch::find()->with('kabupatenKota')->orderBy(['id'=>SORT_DESC])->all();
        return $this->render('index',['models'=>$models]);
    }

    public function actionCreate()
    {
        $model = new RecruitmentBatch(['status'=>RecruitmentBatch::STATUS_DRAFT]);
        return $this->save($model);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        return $this->save($model);
    }

    private function save(RecruitmentBatch $model)
    {
        if ($model->load(Yii::$app->request->post())) {
            $model->location_ids = array_values(array_unique(array_filter(array_map('intval', (array)$model->location_ids))));

            if ($model->location_ids) {
                $validIds = array_map('intval', KabupatenKota::find()
                    ->select('id')
                    ->where(['id' => $model->location_ids, 'is_active' => 1])
                    ->column());
                sort($validIds);
                $requested = $model->location_ids;
                sort($requested);
                if ($validIds !== $requested) {
                    $model->addError('location_ids', 'Ada lokasi yang tidak tersedia. Silakan pilih ulang.');
                }
            }

            // Tetap isi kolom lama sebagai fallback untuk kompatibilitas data lama.
            $model->kabupaten_kota_id = $model->location_ids ? (int)$model->location_ids[0] : null;

            if (!$model->slug) $model->slug=$this->slugify($model->title.'-batch-'.$model->batch_number);
            if (!$model->code) $model->code='GSM-'.date('Y').'-B'.str_pad((string)$model->batch_number,2,'0',STR_PAD_LEFT);
            $model->created_by=$model->created_by ?: Yii::$app->user->id;

            if (!$model->hasErrors()) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if (!$model->save()) {
                        throw new \RuntimeException('Batch belum dapat disimpan.');
                    }
                    if (!$model->hasLocationTable()) {
                        throw new \RuntimeException('Tabel lokasi batch belum tersedia. Jalankan file pembaruan database V16 terlebih dahulu.');
                    }

                    Yii::$app->db->createCommand()->delete('recruitment_batch_location', ['batch_id' => $model->id])->execute();
                    if ($model->location_ids) {
                        $rows = [];
                        $now = time();
                        foreach ($model->location_ids as $regionId) {
                            $rows[] = [(int)$model->id, (int)$regionId, $now];
                        }
                        Yii::$app->db->createCommand()->batchInsert(
                            'recruitment_batch_location',
                            ['batch_id','kabupaten_kota_id','created_at'],
                            $rows
                        )->execute();
                    }

                    $transaction->commit();
                    Yii::$app->session->setFlash('success','Batch rekrutmen berhasil disimpan.');
                    return $this->redirect(['index']);
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    Yii::error($e, __METHOD__);
                    $model->addError('location_ids', $e->getMessage());
                }
            }
        }

        $regions=KabupatenKota::find()->where(['is_active'=>1])->orderBy(['nama'=>SORT_ASC])->all();
        return $this->render($model->isNewRecord?'create':'update',['model'=>$model,'regions'=>$regions]);
    }

    private function findModel($id)
    {
        $m=RecruitmentBatch::findOne($id);
        if(!$m) throw new NotFoundHttpException('Batch tidak ditemukan.');
        return $m;
    }

    private function slugify($text)
    {
        $text=strtolower(trim($text));
        $text=preg_replace('/[^a-z0-9]+/','-',$text);
        return trim($text,'-');
    }
}
