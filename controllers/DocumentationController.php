<?php
namespace app\controllers;

use app\models\DocumentationAlbum;
use app\models\KabupatenKota;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DocumentationController extends Controller
{
    public $layout = 'guest';

    public function actionIndex()
    {
        $regionSlug = trim((string) \Yii::$app->request->get('daerah', ''));
        $year = (int) \Yii::$app->request->get('tahun', 0);

        $query = DocumentationAlbum::publishedQuery()
            ->with(['region', 'batch', 'coverPhoto', 'firstPhoto']);

        if ($regionSlug !== '') {
            $query->joinWith('region')
                ->andWhere(['kabupaten_kota.slug' => $regionSlug]);
        }

        if ($year > 0) {
            $query->andWhere(['documentation_album.year' => $year]);
        }

        $pagination = new Pagination([
            'totalCount' => (clone $query)->count(),
            'pageSize' => 18,
            'pageSizeParam' => false,
        ]);

        $models = $query
            ->orderBy([
                'documentation_album.year' => SORT_DESC,
                'documentation_album.published_at' => SORT_DESC,
                'documentation_album.id' => SORT_DESC,
            ])
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $regions = KabupatenKota::find()
            ->innerJoin('documentation_album', 'documentation_album.kabupaten_kota_id = kabupaten_kota.id')
            ->where(['documentation_album.status' => DocumentationAlbum::STATUS_PUBLISHED])
            ->andWhere(['kabupaten_kota.is_active' => 1])
            ->distinct()
            ->orderBy(['kabupaten_kota.nama' => SORT_ASC])
            ->all();

        $years = DocumentationAlbum::publishedQuery()
            ->select('documentation_album.year')
            ->distinct()
            ->orderBy(['documentation_album.year' => SORT_DESC])
            ->column();

        return $this->render('index', compact(
            'models', 'pagination', 'regions', 'years', 'regionSlug', 'year'
        ));
    }

    public function actionView($slug)
    {
        $model = DocumentationAlbum::publishedQuery()
            ->andWhere(['documentation_album.slug' => $slug])
            ->with(['region', 'batch', 'photos'])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('Dokumentasi pengabdian tidak ditemukan.');
        }

        return $this->render('view', ['model' => $model]);
    }
}
