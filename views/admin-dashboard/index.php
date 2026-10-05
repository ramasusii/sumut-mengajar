<?php
use yii\helpers\Html;

$this->title = 'Dashboard';
?>

<div class="row">
    <div class="col-md-3 col-sm-6">
        <div class="small-box bg-aqua">
            <div class="inner"><h3><?= (int)$batchCount ?></h3><p>Total Batch</p></div>
            <div class="icon"><i class="fa fa-calendar"></i></div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="small-box bg-green">
            <div class="inner"><h3><?= (int)$openBatchCount ?></h3><p>Batch Sedang Dibuka</p></div>
            <div class="icon"><i class="fa fa-bullhorn"></i></div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="small-box bg-yellow">
            <div class="inner"><h3><?= (int)$applicantCount ?></h3><p>Akun Peserta</p></div>
            <div class="icon"><i class="fa fa-users"></i></div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="small-box bg-red">
            <div class="inner"><h3><?= (int)$submittedCount ?></h3><p>Menunggu Verifikasi</p></div>
            <div class="icon"><i class="fa fa-file-text"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="box box-success">
            <div class="box-header with-border"><h3 class="box-title">Rekrutmen</h3></div>
            <div class="box-body">
                <p><b><?= (int)$revisionCount ?></b> pendaftar sedang diminta memperbaiki berkas.</p>
                <p>Gunakan halaman verifikasi untuk memeriksa jawaban, dokumen, dan perkembangan seleksi peserta.</p>
                <?= Html::a('<i class="fa fa-users"></i> Pendaftar & Verifikasi', ['/admin-applicant/index'], ['class'=>'btn btn-success']) ?>
                <?php if($isContentAdmin): ?>
                    <?= Html::a('<i class="fa fa-calendar"></i> Kelola Batch', ['/admin-recruitment/index'], ['class'=>'btn btn-default']) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if($isContentAdmin): ?>
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Website & Alumni</h3></div>
            <div class="box-body">
                <div class="row" style="margin-bottom:15px">
                    <div class="col-xs-4"><h3 style="margin:0"><?= (int)$alumniCount ?></h3><small>Total Alumni</small></div>
                    <div class="col-xs-4"><h3 style="margin:0"><?= (int)$pendingAlumniCount ?></h3><small>Menunggu Verifikasi</small></div>
                    <div class="col-xs-4"><h3 style="margin:0"><?= (int)$heroCount ?></h3><small>Banner Aktif</small></div>
                </div>
                <?= Html::a('<i class="fa fa-graduation-cap"></i> Alumni & Karya', ['/admin-alumni/index'], ['class'=>'btn btn-primary']) ?>
                <?= Html::a('<i class="fa fa-picture-o"></i> Hero Banner', ['/admin-hero/index'], ['class'=>'btn btn-default']) ?>
                <?= Html::a('<i class="fa fa-external-link"></i> Buka Website', ['/site/index'], ['class'=>'btn btn-default','target'=>'_blank','rel'=>'noopener']) ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
