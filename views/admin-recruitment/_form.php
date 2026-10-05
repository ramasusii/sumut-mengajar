<?php
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\RecruitmentBatch;

$form = ActiveForm::begin();
$regionItems = ArrayHelper::map($regions, 'id', fn($r) => $r->label);
?>

<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-md-6"><?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?></div>
            <div class="col-md-3"><?= $form->field($model, 'batch_number')->input('number') ?></div>
            <div class="col-md-3"><?= $form->field($model, 'quota')->input('number') ?></div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'location_ids')->checkboxList($regionItems, ['class' => 'batch-location-picker'])->label('Lokasi Pengabdian') ?>
                <p class="help-block" style="margin-top:-8px">Satu batch dapat memiliki beberapa kabupaten/kota. Centang semua lokasi tujuan pengabdian.</p>
            </div>
            <div class="col-md-3"><?= $form->field($model, 'code')->textInput(['placeholder' => 'Akan dibuat otomatis jika dikosongkan']) ?></div>
            <div class="col-md-3">
                <?= $form->field($model, 'status')->dropDownList([
                    RecruitmentBatch::STATUS_DRAFT => 'Draf',
                    RecruitmentBatch::STATUS_OPEN => 'Pendaftaran Dibuka',
                    RecruitmentBatch::STATUS_CLOSED => 'Pendaftaran Ditutup',
                    RecruitmentBatch::STATUS_ANNOUNCED => 'Pengumuman Terbit',
                ]) ?>
            </div>
        </div>

        <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

        <div class="row">
            <div class="col-md-3"><?= $form->field($model, 'registration_start')->input('date')->label('Mulai Pendaftaran') ?></div>
            <div class="col-md-3"><?= $form->field($model, 'registration_end')->input('date')->label('Tutup Pendaftaran') ?></div>
            <div class="col-md-3"><?= $form->field($model, 'interview_date')->input('date')->label('Wawancara') ?></div>
            <div class="col-md-3"><?= $form->field($model, 'announcement_date')->input('date')->label('Pengumuman') ?></div>
        </div>
        <div class="row">
            <div class="col-md-4"><?= $form->field($model, 'briefing_date')->input('date')->label('Pembekalan') ?></div>
            <div class="col-md-4"><?= $form->field($model, 'activity_start')->input('date')->label('Mulai Pengabdian') ?></div>
            <div class="col-md-4"><?= $form->field($model, 'activity_end')->input('date')->label('Selesai Pengabdian') ?></div>
        </div>

        <div class="row">
            <div class="col-md-6"><?= $form->field($model, 'requirements')->textarea(['rows' => 6, 'placeholder' => 'Satu persyaratan per baris']) ?></div>
            <div class="col-md-6"><?= $form->field($model, 'benefits')->textarea(['rows' => 6, 'placeholder' => 'Satu manfaat per baris']) ?></div>
        </div>

        <button class="btn btn-primary" type="submit">Simpan Batch</button>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php
$this->registerCss(<<<'CSS'
.batch-location-picker{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:7px 10px;max-height:270px;overflow:auto;padding:10px;border:1px solid #d9e2dc;border-radius:8px;background:#fbfcfb}
.batch-location-picker label{display:flex;align-items:flex-start;gap:7px;margin:0;padding:7px 9px;border:1px solid #edf1ee;border-radius:7px;background:#fff;font-weight:500;cursor:pointer}
.batch-location-picker label:hover{border-color:#b9d5c3;background:#f4faf6}.batch-location-picker input{margin-top:2px!important}
@media(max-width:767px){.batch-location-picker{grid-template-columns:1fr}}
CSS);
?>
