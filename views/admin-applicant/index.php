<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Data Pendaftar';
$statusLabels = [
    'draft'=>'Belum Selesai','submitted'=>'Menunggu Verifikasi','revision_required'=>'Perlu Perbaikan',
    'verified'=>'Terverifikasi','administration_pass'=>'Lolos Administrasi','interview'=>'Tahap Wawancara',
    'interview_pass'=>'Lolos Wawancara','final_pass'=>'Lolos Akhir','rejected'=>'Belum Lolos',
];
$statusBadge = static fn($status) => match($status){
    'submitted'=>'label-warning','revision_required'=>'label-danger','verified'=>'label-info',
    'administration_pass','interview_pass','final_pass'=>'label-success','interview'=>'label-primary','rejected'=>'label-default',default=>'label-default'
};
?>
<div class="row">
    <div class="col-sm-3"><div class="small-box bg-aqua"><div class="inner"><h3><?= (int)$summary['total'] ?></h3><p>Total Pendaftar</p></div><div class="icon"><i class="fa fa-users"></i></div></div></div>
    <div class="col-sm-3"><div class="small-box bg-yellow"><div class="inner"><h3><?= (int)$summary['submitted'] ?></h3><p>Menunggu Verifikasi</p></div><div class="icon"><i class="fa fa-clock-o"></i></div></div></div>
    <div class="col-sm-3"><div class="small-box bg-red"><div class="inner"><h3><?= (int)$summary['revision'] ?></h3><p>Perlu Perbaikan</p></div><div class="icon"><i class="fa fa-refresh"></i></div></div></div>
    <div class="col-sm-3"><div class="small-box bg-green"><div class="inner"><h3><?= (int)$summary['final'] ?></h3><p>Lolos Akhir</p></div><div class="icon"><i class="fa fa-check-circle"></i></div></div></div>
</div>

<div class="box box-success">
    <div class="box-header with-border"><h3 class="box-title">Filter & Pencarian</h3></div>
    <div class="box-body">
        <?= Html::beginForm(['index'],'get',['class'=>'row']) ?>
        <div class="col-md-5"><label>Cari Pendaftar</label><?= Html::textInput('q',$q,['class'=>'form-control','placeholder'=>'Nama, kode pendaftaran, WhatsApp, atau email']) ?></div>
        <div class="col-md-3"><label>Status</label><?= Html::dropDownList('status',$status,$statusLabels,['class'=>'form-control','prompt'=>'Semua Status']) ?></div>
        <div class="col-md-3"><label>Batch</label><?php $opts=[]; foreach($batches as $b){$opts[$b->id]='Batch '.$b->batch_number.' — '.$b->title;} ?><?= Html::dropDownList('batch_id',$batchId,$opts,['class'=>'form-control','prompt'=>'Semua Batch']) ?></div>
        <div class="col-md-1"><label>&nbsp;</label><button class="btn btn-success btn-block" type="submit"><i class="fa fa-search"></i></button></div>
        <?= Html::endForm() ?>
    </div>
</div>

<div class="box">
    <div class="box-header with-border"><h3 class="box-title">Daftar Pendaftar</h3><div class="box-tools pull-right"><span class="text-muted"><?= (int)$pagination->totalCount ?> data</span></div></div>
    <div class="box-body table-responsive">
        <table class="table table-striped table-hover">
            <thead><tr><th>Kode</th><th>Peserta</th><th>Batch</th><th>Status</th><th>Berkas</th><th>Dikirim</th><th></th></tr></thead>
            <tbody>
            <?php if(!$models): ?><tr><td colspan="7" class="text-center text-muted" style="padding:30px">Tidak ada data yang sesuai filter.</td></tr><?php endif; ?>
            <?php foreach($models as $model): ?>
                <?php $valid=0;$invalid=0;foreach($model->documents as $doc){if($doc->verification_status==='valid')$valid++;elseif($doc->verification_status==='invalid')$invalid++;} ?>
                <tr>
                    <td><b><?= Html::encode($model->application_code) ?></b></td>
                    <td><b><?= Html::encode($model->user->nama ?: ($model->profile->nama_lengkap ?? '-')) ?></b><br><small><?= Html::encode($model->user->whatsapp ?: ($model->profile->nomor_whatsapp ?? $model->user->email ?: '-')) ?></small></td>
                    <td><b>Batch <?= (int)$model->batch->batch_number ?></b><br><small><?= Html::encode($model->batch->kabupatenKota ? $model->batch->kabupatenKota->label : '-') ?></small></td>
                    <td><span class="label <?= $statusBadge($model->status) ?>"><?= Html::encode($statusLabels[$model->status] ?? 'Sedang Diproses') ?></span></td>
                    <td><small><?= count($model->documents) ?> upload<?= $valid?' · '.$valid.' valid':'' ?><?= $invalid?' · '.$invalid.' revisi':'' ?></small></td>
                    <td><?= $model->submitted_at ? Yii::$app->formatter->asDatetime($model->submitted_at,'php:d M Y H:i') : '<span class="text-muted">Belum dikirim</span>' ?></td>
                    <td><?= Html::a('<i class="fa fa-search"></i> Review',['view','id'=>$model->id],['class'=>'btn btn-sm btn-success']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if($pagination->pageCount>1): ?><div class="box-footer clearfix"><?= LinkPager::widget(['pagination'=>$pagination]) ?></div><?php endif; ?>
</div>
