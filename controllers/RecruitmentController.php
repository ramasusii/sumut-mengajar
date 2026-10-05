<?php
namespace app\controllers;
use app\models\RecruitmentBatch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
class RecruitmentController extends Controller
{
    public $layout='guest';
    public function actionIndex(){ $batches=RecruitmentBatch::find()->where(['in','status',[RecruitmentBatch::STATUS_OPEN,RecruitmentBatch::STATUS_CLOSED,RecruitmentBatch::STATUS_ANNOUNCED]])->orderBy(['id'=>SORT_DESC])->all(); return $this->render('index',['batches'=>$batches]); }
    public function actionView($slug){ $model=RecruitmentBatch::findOne(['slug'=>$slug]); if(!$model) throw new NotFoundHttpException('Batch rekrutmen tidak ditemukan.'); return $this->render('view',['model'=>$model]); }
}
