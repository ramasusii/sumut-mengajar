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
                    ['allow' => true, 'roles' => ['developer', 'superAdmin']],
                ],
                'denyCallback' => function () {
                    if (Yii::$app->user->isGuest) {
                        Yii::$app->user->setReturnUrl(Yii::$app->request->url);
                        return Yii::$app->response->redirect(['/site/staff-login']);
                    }
                    if (Yii::$app->user->can('reviewer') || Yii::$app->user->can('adminGsm')) {
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
    public function actionIndex(){ $models=RecruitmentBatch::find()->with('kabupatenKota')->orderBy(['id'=>SORT_DESC])->all(); return $this->render('index',['models'=>$models]); }
    public function actionCreate(){ $model=new RecruitmentBatch(['status'=>RecruitmentBatch::STATUS_DRAFT]); return $this->save($model); }
    public function actionUpdate($id){ $model=$this->findModel($id); return $this->save($model); }
    private function save(RecruitmentBatch $model){
        if($model->load(Yii::$app->request->post())){
            if(!$model->slug) $model->slug=$this->slugify($model->title.'-batch-'.$model->batch_number);
            if(!$model->code) $model->code='GSM-'.date('Y').'-B'.str_pad((string)$model->batch_number,2,'0',STR_PAD_LEFT);
            $model->created_by=$model->created_by ?: Yii::$app->user->id;
            if($model->save()){ Yii::$app->session->setFlash('success','Batch rekrutmen berhasil disimpan.'); return $this->redirect(['index']); }
        }
        $regions=KabupatenKota::find()->where(['is_active'=>1])->orderBy(['nama'=>SORT_ASC])->all();
        return $this->render($model->isNewRecord?'create':'update',['model'=>$model,'regions'=>$regions]);
    }
    private function findModel($id){ $m=RecruitmentBatch::findOne($id); if(!$m) throw new NotFoundHttpException('Batch tidak ditemukan.'); return $m; }
    private function slugify($text){ $text=strtolower(trim($text)); $text=preg_replace('/[^a-z0-9]+/','-',$text); return trim($text,'-'); }
}
