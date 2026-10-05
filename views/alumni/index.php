<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
$this->title='Alumni Sumut Mengajar';
$photoUrl=static fn($path)=>$path?Yii::$app->request->baseUrl.'/'.ltrim($path,'/'):null;
?>
<section class="page-hero alumni-hero"><div class="container"><span class="section-kicker">JEJARING ALUMNI</span><h1>Jejak pengabdian yang terus tumbuh.</h1><p>Kenali alumni Sumut Mengajar, perjalanan karier mereka, serta karya dan riset yang lahir dari pengalaman pengabdian.</p><div class="alumni-hero-actions"><a class="btn-primary-gsm" href="<?= Url::to(['/alumni/register']) ?>">Daftarkan Profil Alumni →</a></div></div></section>
<section class="alumni-stats"><div class="container alumni-stats-grid"><div><b><?= (int)$stats['alumni'] ?></b><span>Profil Alumni</span></div><div><b><?= (int)$stats['batches'] ?></b><span>Batch Terwakili</span></div><div><b><?= (int)$stats['institutions'] ?></b><span>Instansi / Perusahaan</span></div><div><b><?= (int)$stats['works'] ?></b><span>Karya & Riset</span></div></div></section>
<section class="section soft"><div class="container">
<div class="alumni-filter-card"><?= Html::beginForm(['/alumni/index'],'get') ?><div class="alumni-filter-grid"><div><label>Cari alumni</label><?= Html::textInput('q',$q,['placeholder'=>'Nama, instansi, jabatan, atau lokasi']) ?></div><div><label>Batch</label><?php $opts=array_combine($batches,$batches); ?><?= Html::dropDownList('batch',$batch,$opts,['prompt'=>'Semua Batch']) ?></div><div><label>Sektor</label><?php $sopts=array_combine($sectors,$sectors); ?><?= Html::dropDownList('sector',$sector,$sopts,['prompt'=>'Semua Sektor']) ?></div><div><button type="submit">Cari</button></div></div><?= Html::endForm() ?></div>
<div class="alumni-grid">
<?php if(!$models): ?><div class="empty-card"><h3>Belum ada alumni yang sesuai pencarian.</h3><p>Coba ubah kata kunci atau filter.</p></div><?php endif; ?>
<?php foreach($models as $m): ?><article class="alumni-card"><a class="alumni-photo" href="<?= Url::to(['/alumni/view','slug'=>$m->slug]) ?>"><?php if($photoUrl($m->photo)): ?><img src="<?= Html::encode($photoUrl($m->photo)) ?>" alt="<?= Html::encode($m->nama_lengkap) ?>"><?php else: ?><span><?= Html::encode(mb_strtoupper(mb_substr($m->nama_lengkap,0,1))) ?></span><?php endif; ?></a><div class="alumni-card-body"><small>ALUMNI BATCH <?= (int)$m->batch_number ?> · <?= Html::encode(mb_strtoupper($m->location_name ?: 'SUMATERA UTARA')) ?></small><h2><a href="<?= Url::to(['/alumni/view','slug'=>$m->slug]) ?>"><?= Html::encode($m->nama_lengkap) ?></a></h2><p class="alumni-job"><?= Html::encode($m->current_position ?: 'Alumni Sumut Mengajar') ?><?= $m->current_institution?' · '.Html::encode($m->current_institution):'' ?></p><?php if($m->publications): ?><div class="alumni-work-count">📚 <?= count($m->publications) ?> karya tercatat</div><?php endif; ?><a class="text-link" href="<?= Url::to(['/alumni/view','slug'=>$m->slug]) ?>">Lihat perjalanan →</a></div></article><?php endforeach; ?>
</div>
<?php if($pagination->pageCount>1): ?><div class="alumni-pagination"><?= LinkPager::widget(['pagination'=>$pagination]) ?></div><?php endif; ?>
</div></section>
