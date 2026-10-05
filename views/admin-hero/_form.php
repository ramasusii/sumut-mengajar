<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$form=ActiveForm::begin(['options'=>['enctype'=>'multipart/form-data']]);
?>
<div class="box box-success"><div class="box-body">
<div class="row"><div class="col-md-8"><?= $form->field($model,'title')->textInput(['maxlength'=>true])->label('Judul Utama') ?></div><div class="col-md-4"><?= $form->field($model,'badge')->textInput(['placeholder'=>'Contoh: OPEN RECRUITMENT'])->label('Label Kecil') ?></div></div>
<?= $form->field($model,'subtitle')->textarea(['rows'=>3])->label('Deskripsi Singkat') ?>
<div class="row"><div class="col-md-6"><label>Banner Desktop <?= $model->isNewRecord?'*':'' ?></label><input type="file" class="form-control" name="desktop_upload" accept="image/jpeg,image/png,image/webp"><p class="help-block">Rekomendasi 1920×850 px. JPG/PNG/WebP maks. 5 MB.</p><?php if($model->desktop_image): ?><small>Sudah ada: <?= Html::encode($model->desktop_image) ?></small><?php endif; ?></div><div class="col-md-6"><label>Banner Mobile</label><input type="file" class="form-control" name="mobile_upload" accept="image/jpeg,image/png,image/webp"><p class="help-block">Rekomendasi 1080×1350 px. Jika kosong, desktop dipakai otomatis.</p><?php if($model->mobile_image): ?><small>Sudah ada: <?= Html::encode($model->mobile_image) ?></small><?php endif; ?></div></div><hr>
<div class="row"><div class="col-md-3"><?= $form->field($model,'primary_label')->textInput(['placeholder'=>'Daftar Sekarang'])->label('Tombol Utama') ?></div><div class="col-md-3"><?= $form->field($model,'primary_url')->textInput(['placeholder'=>'/rekrutmen'])->label('URL Tombol Utama') ?></div><div class="col-md-3"><?= $form->field($model,'secondary_label')->textInput(['placeholder'=>'Tentang Kami'])->label('Tombol Kedua') ?></div><div class="col-md-3"><?= $form->field($model,'secondary_url')->textInput(['placeholder'=>'/tentang'])->label('URL Tombol Kedua') ?></div></div>
<div class="row"><div class="col-md-3"><?= $form->field($model,'sort_order')->input('number')->label('Urutan') ?></div><div class="col-md-3"><?= $form->field($model,'start_at')->input('datetime-local',['value'=>$model->start_at?date('Y-m-d\TH:i',strtotime($model->start_at)):null])->label('Mulai Tayang') ?></div><div class="col-md-3"><?= $form->field($model,'end_at')->input('datetime-local',['value'=>$model->end_at?date('Y-m-d\TH:i',strtotime($model->end_at)):null])->label('Selesai Tayang') ?></div><div class="col-md-3"><?= $form->field($model,'is_published')->checkbox()->label('Publikasikan') ?></div></div>
<button class="btn btn-success" type="submit"><i class="fa fa-save"></i> Simpan Banner</button> <?= Html::a('Batal',['index'],['class'=>'btn btn-default']) ?>
</div></div>
<?php ActiveForm::end(); ?>
