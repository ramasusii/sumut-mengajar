<?php
use app\models\DocumentationAlbum;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = 'Dokumentasi Pengabdian';

$regionItems = ArrayHelper::map($regions, 'id', 'label');
$yearItems = array_combine($years, $years);
$statusItems = [
    DocumentationAlbum::STATUS_DRAFT => 'Draf',
    DocumentationAlbum::STATUS_PUBLISHED => 'Terbit',
];
?>
<div class="row">
    <div class="col-md-4">
        <div class="small-box bg-aqua">
            <div class="inner"><h3><?= (int) $stats['albums'] ?></h3><p>Total Album</p></div>
            <div class="icon"><i class="fa fa-folder-open"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="small-box bg-green">
            <div class="inner"><h3><?= (int) $stats['published'] ?></h3><p>Album Terbit</p></div>
            <div class="icon"><i class="fa fa-globe"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="small-box bg-yellow">
            <div class="inner"><h3><?= (int) $stats['photos'] ?></h3><p>Total Foto</p></div>
            <div class="icon"><i class="fa fa-picture-o"></i></div>
        </div>
    </div>
</div>

<div class="box box-success doc-admin-box-v22">
    <div class="box-header with-border">
        <div>
            <h3 class="box-title">Album Dokumentasi</h3>
            <p class="text-muted" style="margin:5px 0 0">Kelola dokumentasi berdasarkan daerah, tahun, dan album kegiatan.</p>
        </div>
        <div class="box-tools">
            <?= Html::a('<i class="fa fa-plus"></i> Buat Album', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
            <?= Html::a('<i class="fa fa-external-link"></i> Lihat Website', ['/documentation/index'], ['class' => 'btn btn-default btn-sm', 'target' => '_blank']) ?>
        </div>
    </div>

    <div class="box-body">
        <?= Html::beginForm(['index'], 'get', ['class' => 'doc-filter-v22']) ?>
            <?= Html::textInput('q', $q, ['class' => 'form-control', 'placeholder' => 'Cari nama album...']) ?>
            <?= Html::dropDownList('region_id', $regionId ?: '', $regionItems, ['class' => 'form-control', 'prompt' => 'Semua Daerah']) ?>
            <?= Html::dropDownList('year', $year ?: '', $yearItems, ['class' => 'form-control', 'prompt' => 'Semua Tahun']) ?>
            <?= Html::dropDownList('status', $status, $statusItems, ['class' => 'form-control', 'prompt' => 'Semua Status']) ?>
            <button class="btn btn-default"><i class="fa fa-search"></i> Terapkan</button>
            <?= Html::a('Reset', ['index'], ['class' => 'btn btn-link']) ?>
        <?= Html::endForm() ?>

        <?php if (!$models): ?>
            <div class="doc-admin-empty-v22">
                <i class="fa fa-picture-o"></i>
                <h4>Belum ada album dokumentasi.</h4>
                <p>Buat album pertama, pilih daerah dan tahun, lalu unggah foto kegiatan.</p>
                <?= Html::a('Buat Album', ['create'], ['class' => 'btn btn-success']) ?>
            </div>
        <?php else: ?>
            <div class="doc-admin-grid-v22">
                <?php foreach ($models as $model): ?>
                    <?php
                        $cover = $model->getDisplayCoverPhoto();
                        $coverUrl = $cover && $cover->thumb_path
                            ? Yii::$app->request->baseUrl . '/' . ltrim($cover->thumb_path, '/')
                            : null;
                    ?>
                    <article class="doc-admin-card-v22">
                        <div class="doc-admin-cover-v22">
                            <?php if ($coverUrl): ?>
                                <img src="<?= Html::encode($coverUrl) ?>" alt="<?= Html::encode($model->title) ?>">
                            <?php else: ?>
                                <div class="doc-admin-placeholder-v22"><i class="fa fa-picture-o"></i></div>
                            <?php endif; ?>
                            <span class="doc-admin-year-v22"><?= (int) $model->year ?></span>
                            <span class="doc-admin-status-v22 <?= $model->status === DocumentationAlbum::STATUS_PUBLISHED ? 'is-live' : '' ?>">
                                <?= Html::encode($statusItems[$model->status] ?? $model->status) ?>
                            </span>
                        </div>

                        <div class="doc-admin-card-body-v22">
                            <div class="doc-admin-region-v22"><?= Html::encode($model->region ? $model->region->label : '-') ?></div>
                            <h4><?= Html::encode($model->title) ?></h4>
                            <p><?= Html::encode($model->description ?: 'Belum ada deskripsi album.') ?></p>

                            <div class="doc-admin-meta-v22">
                                <span><i class="fa fa-picture-o"></i> <?= (int) $model->getPhotoCount() ?> foto</span>
                                <?php if ($model->batch): ?><span><i class="fa fa-calendar"></i> Batch <?= (int) $model->batch->batch_number ?></span><?php endif; ?>
                            </div>
                        </div>

                        <div class="doc-admin-actions-v22">
                            <?= Html::a('<i class="fa fa-picture-o"></i> Kelola Foto', ['photos', 'id' => $model->id], ['class' => 'btn btn-success btn-sm']) ?>
                            <?= Html::a('<i class="fa fa-pencil"></i> Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-default btn-sm']) ?>
                            <?= Html::a('<i class="fa fa-trash"></i>', ['delete', 'id' => $model->id], [
                                'class' => 'btn btn-danger btn-sm',
                                'data' => ['method' => 'post', 'confirm' => 'Hapus album beserta seluruh fotonya?'],
                            ]) ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?= LinkPager::widget(['pagination' => $pagination]) ?>
        <?php endif; ?>
    </div>
</div>

<?php
$this->registerCss(<<<'CSS'
.doc-admin-box-v22 .box-header{display:flex;justify-content:space-between;align-items:flex-start;gap:16px}
.doc-filter-v22{display:grid;grid-template-columns:1.4fr 1fr .7fr .8fr auto auto;gap:8px;margin-bottom:18px}
.doc-admin-grid-v22{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}
.doc-admin-card-v22{overflow:hidden;border:1px solid #e4e9e6;border-radius:14px;background:#fff;box-shadow:0 8px 24px rgba(18,42,28,.05)}
.doc-admin-cover-v22{position:relative;aspect-ratio:16/10;background:#edf2ef;overflow:hidden}.doc-admin-cover-v22 img{width:100%;height:100%;object-fit:cover;display:block}
.doc-admin-placeholder-v22{height:100%;display:flex;align-items:center;justify-content:center;color:#9ba79f;font-size:34px}
.doc-admin-year-v22,.doc-admin-status-v22{position:absolute;top:10px;padding:5px 9px;border-radius:999px;font-size:10px;font-weight:800}
.doc-admin-year-v22{left:10px;background:rgba(255,255,255,.92);color:#304137}.doc-admin-status-v22{right:10px;background:#f0f2f1;color:#68736c}.doc-admin-status-v22.is-live{background:#e4f7ec;color:#147443}
.doc-admin-card-body-v22{padding:15px}.doc-admin-region-v22{color:#16824a;font-size:10px;font-weight:800;letter-spacing:.6px;text-transform:uppercase}.doc-admin-card-body-v22 h4{margin:5px 0 7px;font-weight:800}.doc-admin-card-body-v22 p{min-height:42px;color:#6d7972;font-size:12px;line-height:1.5}
.doc-admin-meta-v22{display:flex;gap:12px;flex-wrap:wrap;color:#7c8880;font-size:11px}.doc-admin-actions-v22{display:flex;gap:6px;padding:12px 15px;border-top:1px solid #edf1ee;background:#fbfcfb}
.doc-admin-empty-v22{text-align:center;padding:55px 20px;color:#78857d}.doc-admin-empty-v22>i{font-size:42px;color:#abc1b2}.doc-admin-empty-v22 h4{margin:12px 0 5px;color:#314239}
@media(max-width:1100px){.doc-filter-v22{grid-template-columns:repeat(2,minmax(0,1fr))}.doc-admin-grid-v22{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:700px){.doc-admin-box-v22 .box-header{flex-direction:column}.doc-filter-v22,.doc-admin-grid-v22{grid-template-columns:1fr}}
CSS);
?>
