<?php
namespace app\controllers;

use app\models\Post;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ArticleController extends Controller
{
    public $layout = 'guest';

    public function actionIndex()
    {
        $query = Post::publishedQuery()->with('category');

        $pagination = new Pagination([
            'totalCount' => (clone $query)->count(),
            'pageSize' => 12,
            'pageSizeParam' => false,
        ]);

        $models = $query
            ->orderBy(['published_at' => SORT_DESC, 'id' => SORT_DESC])
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('index', compact('models','pagination'));
    }

    public function actionView($slug)
    {
        $model = Post::publishedQuery()
            ->andWhere(['slug' => $slug])
            ->with('category')
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('Artikel tidak ditemukan.');
        }

        return $this->render('view', ['model' => $model]);
    }
}
