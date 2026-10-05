<?php
use yii\helpers\Html;
use yii\helpers\Url;
$this->title='Hero Banner';
$publicUrl=static function($path){if(!$path)return null;return Yii::$app->request->baseUrl.'/'.ltrim($path,'/');};
?>
<p><?= Html::a('<i class="fa fa-plus"></i> Tambah Banner',['create'],['class'=>'btn btn-success']) ?> <a class="btn btn-default" target="_blank" href="<?= Url::to(['/site/index']) ?>"><i class="fa fa-external-link"></i> Lihat Homepage</a></p>
<div class="box"><div class="box-header with-border"><h3 class="box-title">Slider Homepage</h3></div><div class="box-body table-responsive">
<table class="table table-striped table-hover"><thead><tr><th width="150">Preview</th><th>Konten</th><th>Jadwal</th><th>Urutan</th><th>Status</th><th width="150"></th></tr></thead><tbody>
<?php if(!$models): ?><tr><td colspan="6" class="text-center text-muted" style="padding:30px">Belum ada banner. Homepage akan memakai hero bawaan.</td></tr><?php endif; ?>
<?php foreach($models as $m): ?><tr>
<td><?php if($m->desktop_image): ?><img src="<?= Html::encode($publicUrl($m->desktop_image)) ?>" style="width:130px;height:70px;object-fit:cover;border-radius:6px"><?php endif; ?></td>
<td><b><?= Html::encode($m->title) ?></b><br><small><?= Html::encode($m->badge ?: 'Hero Banner') ?></small></td>
<td><small><?= $m->start_at?Yii::$app->formatter->asDatetime($m->start_at):'Langsung tayang' ?><br>s.d. <?= $m->end_at?Yii::$app->formatter->asDatetime($m->end_at):'Tanpa batas' ?></small></td>
<td><?= (int)$m->sort_order ?></td>
<td><span class="label <?= $m->is_published?'label-success':'label-default' ?>"><?= $m->is_published?'Aktif':'Nonaktif' ?></span></td>
<td><?= Html::a('Edit',['update','id'=>$m->id],['class'=>'btn btn-xs btn-primary']) ?> <?= Html::beginForm(['toggle','id'=>$m->id],'post',['style'=>'display:inline']) ?><button class="btn btn-xs btn-warning" type="submit"><?= $m->is_published?'Nonaktifkan':'Aktifkan' ?></button><?= Html::endForm() ?> <?= Html::beginForm(['delete','id'=>$m->id],'post',['style'=>'display:inline','onsubmit'=>"return confirm('Hapus banner ini?')"]) ?><button class="btn btn-xs btn-danger" type="submit"><i class="fa fa-trash"></i></button><?= Html::endForm() ?></td>
</tr><?php endforeach; ?>
</tbody></table></div></div>
