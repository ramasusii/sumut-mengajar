<?php
use app\models\AlumniProfile;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$form=ActiveForm::begin(['options'=>['enctype'=>'multipart/form-data']]);
?>
<div class="box box-success"><div class="box-body">
<div class="row"><div class="col-md-6"><?= $form->field($model,'nama_lengkap')->textInput() ?></div><div class="col-md-3"><?= $form->field($model,'whatsapp')->textInput(['placeholder'=>'62812...']) ?></div><div class="col-md-3"><?= $form->field($model,'batch_number')->input('number') ?></div></div>
<div class="row"><div class="col-md-3"><?= $form->field($model,'batch_year')->input('number') ?></div><div class="col-md-5"><?= $form->field($model,'location_name')->textInput(['placeholder'=>'Kabupaten Samosir']) ?></div><div class="col-md-4"><label>Foto Profil</label><input class="form-control" type="file" name="photo_upload" accept="image/jpeg,image/png,image/webp"><?php if($model->photo): ?><small><?= Html::encode($model->photo) ?></small><?php endif; ?></div></div>
<hr><div class="row"><div class="col-md-6"><?= $form->field($model,'current_position')->textInput()->label('Jabatan Saat Ini') ?></div><div class="col-md-6"><?= $form->field($model,'current_institution')->textInput()->label('Instansi / Perusahaan Saat Ini') ?></div></div>
<div class="row"><div class="col-md-4"><?= $form->field($model,'sector')->textInput(['placeholder'=>'Pendidikan, Pemerintahan, Teknologi, dll.']) ?></div><div class="col-md-4"><?= $form->field($model,'work_city')->textInput()->label('Kota Bekerja') ?></div><div class="col-md-4"><?= $form->field($model,'verification_status')->dropDownList([AlumniProfile::STATUS_PENDING=>'Menunggu Verifikasi',AlumniProfile::STATUS_VERIFIED=>'Terverifikasi',AlumniProfile::STATUS_REJECTED=>'Ditolak']) ?></div></div>
<div class="row"><div class="col-md-6"><?= $form->field($model,'instagram')->textInput(['placeholder'=>'@username']) ?></div><div class="col-md-6"><?= $form->field($model,'linkedin')->textInput(['placeholder'=>'https://linkedin.com/in/...']) ?></div></div>
<?= $form->field($model,'bio')->textarea(['rows'=>5])->label('Bio / Cerita Singkat Alumni') ?>
<div class="row"><div class="col-md-4"><?= $form->field($model,'consent_public')->checkbox()->label('Persetujuan publikasi profil sudah tercatat') ?></div><div class="col-md-4"><?= $form->field($model,'is_public')->checkbox()->label('Tampilkan di website') ?></div><div class="col-md-4"><?= $form->field($model,'is_featured')->checkbox()->label('Jadikan alumni unggulan') ?></div></div>
<button class="btn btn-success" type="submit"><i class="fa fa-save"></i> Simpan Alumni</button> <?= Html::a('Batal',['index'],['class'=>'btn btn-default']) ?>
</div></div><?php ActiveForm::end(); ?>
