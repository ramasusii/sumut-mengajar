<?php
use app\models\AlumniProfile;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
$this->title='Alumni & Karya';
$statusLabels=['pending'=>'Menunggu Verifikasi','verified'=>'Terverifikasi','rejected'=>'Ditolak'];
?>
<div class="row">
<div class="col-sm-3"><div class="small-box bg-aqua"><div class="inner"><h3><?= (int)$summary['total'] ?></h3><p>Total Alumni</p></div><div class="icon"><i class="fa fa-graduation-cap"></i></div></div></div>
<div class="col-sm-3"><div class="small-box bg-yellow"><div class="inner"><h3><?= (int)$summary['pending'] ?></h3><p>Menunggu Verifikasi</p></div><div class="icon"><i class="fa fa-clock-o"></i></div></div></div>
<div class="col-sm-3"><div class="small-box bg-green"><div class="inner"><h3><?= (int)$summary['public'] ?></h3><p>Profil Publik</p></div><div class="icon"><i class="fa fa-globe"></i></div></div></div>
<div class="col-sm-3"><div class="small-box bg-purple"><div class="inner"><h3><?= (int)$summary['works'] ?></h3><p>Karya Tercatat</p></div><div class="icon"><i class="fa fa-book"></i></div></div></div>
</div>
<p><?= Html::a('<i class="fa fa-plus"></i> Tambah Alumni',['create'],['class'=>'btn btn-success']) ?> <?= Html::beginForm(['sync-finalists'],'post',['style'=>'display:inline','onsubmit'=>"return confirm('Sinkronkan semua peserta berstatus Lolos Akhir menjadi data alumni internal?')"]) ?><button class="btn btn-primary" type="submit"><i class="fa fa-refresh"></i> Sinkronkan Lulusan</button><?= Html::endForm() ?> <a class="btn btn-default" target="_blank" href="<?= Url::to(['/alumni/index']) ?>"><i class="fa fa-external-link"></i> Halaman Alumni</a></p>
<div class="box box-success"><div class="box-header with-border"><h3 class="box-title">Filter Alumni</h3></div><div class="box-body">
<?= Html::beginForm(['index'],'get',['class'=>'row']) ?><div class="col-md-5"><label>Cari</label><?= Html::textInput('q',$q,['class'=>'form-control','placeholder'=>'Nama, WhatsApp, instansi, atau jabatan']) ?></div><div class="col-md-3"><label>Status</label><?= Html::dropDownList('status',$status,$statusLabels,['class'=>'form-control','prompt'=>'Semua Status']) ?></div><div class="col-md-3"><label>Batch</label><?php $opts=array_combine($batches,$batches); ?><?= Html::dropDownList('batch',$batch,$opts,['class'=>'form-control','prompt'=>'Semua Batch']) ?></div><div class="col-md-1"><label>&nbsp;</label><button class="btn btn-success btn-block"><i class="fa fa-search"></i></button></div><?= Html::endForm() ?>
</div></div>
<div class="box"><div class="box-body table-responsive"><table class="table table-striped table-hover"><thead><tr><th>Alumni</th><th>Batch</th><th>Karier Saat Ini</th><th>Status</th><th>Publik</th><th></th></tr></thead><tbody>
<?php if(!$models): ?><tr><td colspan="6" class="text-center text-muted" style="padding:30px">Belum ada data alumni.</td></tr><?php endif; ?>
<?php foreach($models as $m): ?><tr><td><b><?= Html::encode($m->nama_lengkap) ?></b><br><small><?= Html::encode($m->whatsapp) ?></small></td><td>Batch <?= (int)$m->batch_number ?><br><small><?= Html::encode($m->location_name ?: '-') ?></small></td><td><?= Html::encode($m->current_position ?: '-') ?><br><small><?= Html::encode($m->current_institution ?: '-') ?></small></td><td><span class="label <?= $m->verification_status==='verified'?'label-success':($m->verification_status==='pending'?'label-warning':'label-default') ?>"><?= Html::encode($statusLabels[$m->verification_status]??$m->verification_status) ?></span></td><td><?= $m->is_public&&$m->consent_public?'<span class="label label-success">Tayang</span>':'<span class="label label-default">Privat</span>' ?><?= $m->is_featured?' <span class="label label-primary">Unggulan</span>':'' ?></td><td><?= Html::a('Review',['view','id'=>$m->id],['class'=>'btn btn-xs btn-success']) ?> <?= Html::a('Edit',['update','id'=>$m->id],['class'=>'btn btn-xs btn-default']) ?></td></tr><?php endforeach; ?>
</tbody></table></div><?php if($pagination->pageCount>1): ?><div class="box-footer clearfix"><?= LinkPager::widget(['pagination'=>$pagination]) ?></div><?php endif; ?></div>
