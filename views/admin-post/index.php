<?php
use app\models\Post;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = 'Artikel & Berita';

$statusLabels = [
    Post::STATUS_DRAFT => 'Draf',
    Post::STATUS_PUBLISHED => 'Terbit',
    Post::STATUS_ARCHIVED => 'Arsip',
];
?>
<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">Artikel Website</h3>
        <div class="box-tools">
            <?= Html::a('<i class="fa fa-plus"></i> Tulis Artikel', ['create'], ['class'=>'btn btn-success btn-sm']) ?>
        </div>
    </div>
    <div class="box-body">
        <?= Html::beginForm(['index'], 'get', ['class'=>'form-inline', 'style'=>'margin-bottom:15px']) ?>
            <?= Html::textInput('q', $q, ['class'=>'form-control','placeholder'=>'Cari judul artikel']) ?>
            <?= Html::dropDownList('status', $status, $statusLabels, ['class'=>'form-control','prompt'=>'Semua Status']) ?>
            <button class="btn btn-default"><i class="fa fa-search"></i> Cari</button>
            <?= Html::a('Reset', ['index'], ['class'=>'btn btn-link']) ?>
        <?= Html::endForm() ?>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead><tr><th>Judul</th><th>Kategori</th><th>Status</th><th>Tanggal Terbit</th><th>Penulis</th><th width="150"></th></tr></thead>
                <tbody>
                <?php if(!$models): ?><tr><td colspan="6" class="text-center text-muted" style="padding:30px">Belum ada artikel.</td></tr><?php endif; ?>
                <?php foreach($models as $model): ?>
                    <tr>
                        <td><b><?= Html::encode($model->title) ?></b></td>
                        <td><?= Html::encode($model->category ? $model->category->name : '-') ?></td>
                        <td><?= Html::encode($statusLabels[$model->status] ?? $model->status) ?></td>
                        <td><?= $model->published_at ? Yii::$app->formatter->asDatetime($model->published_at) : '-' ?></td>
                        <td><?= Html::encode($model->creator ? ($model->creator->nama ?: $model->creator->username) : '-') ?></td>
                        <td>
                            <?= Html::a('Edit', ['update','id'=>$model->id], ['class'=>'btn btn-xs btn-primary']) ?>
                            <?= Html::a('Hapus', ['delete','id'=>$model->id], [
                                'class'=>'btn btn-xs btn-danger',
                                'data'=>['method'=>'post','confirm'=>'Hapus artikel ini?'],
                            ]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?= LinkPager::widget(['pagination'=>$pagination]) ?>
    </div>
</div>
