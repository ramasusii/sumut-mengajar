<?php
use app\models\DocumentationAlbum;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$regionItems = ArrayHelper::map($regions, 'id', 'label');

$batchItems = [];
foreach ($batches as $batch) {
    $batchItems[$batch->id] = 'Batch ' . $batch->batch_number . ' — ' . $batch->title;
}

$yearItems = [];
for ($year = (int) date('Y') + 1; $year >= 2014; $year--) {
    $yearItems[$year] = $year;
}
?>

<?php $form = ActiveForm::begin(); ?>

<div class="box box-success doc-form-v22">
    <div class="box-header with-border">
        <h3 class="box-title"><?= $model->isNewRecord ? 'Buat Album Dokumentasi' : 'Edit Album Dokumentasi' ?></h3>
    </div>

    <div class="box-body">
        <div class="doc-form-intro-v22">
            <i class="fa fa-info-circle"></i>
            <div>
                <b>Struktur dokumentasi: Daerah → Tahun → Album.</b>
                <span>Satu batch yang berlangsung di beberapa daerah sebaiknya dibuat menjadi album terpisah untuk setiap daerah.</span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <?= $form->field($model, 'title')->textInput([
                    'maxlength' => true,
                    'placeholder' => 'Contoh: Pengabdian Batch 21 — Samosir',
                ]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'year')->dropDownList($yearItems, ['prompt' => 'Pilih tahun']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'kabupaten_kota_id')->dropDownList($regionItems, ['prompt' => 'Pilih daerah']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'batch_id')->dropDownList($batchItems, ['prompt' => 'Tidak terkait batch tertentu']) ?>
            </div>
        </div>

        <?= $form->field($model, 'description')->textarea([
            'rows' => 5,
            'placeholder' => 'Ceritakan singkat kegiatan, sekolah/desa yang dikunjungi, atau momen penting dalam album ini.',
        ]) ?>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'status')->dropDownList([
                    DocumentationAlbum::STATUS_DRAFT => 'Draf — belum tampil di website',
                    DocumentationAlbum::STATUS_PUBLISHED => 'Terbit — tampil di website',
                ]) ?>
            </div>
        </div>
    </div>

    <div class="box-footer">
        <?= Html::submitButton(
            $model->isNewRecord
                ? '<i class="fa fa-arrow-right"></i> Simpan & Kelola Foto'
                : '<i class="fa fa-save"></i> Simpan Perubahan',
            ['class' => 'btn btn-success']
        ) ?>
        <?= Html::a('Kembali', ['index'], ['class' => 'btn btn-default']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php
$this->registerCss(<<<'CSS'
.doc-form-v22{border-radius:14px}.doc-form-intro-v22{display:flex;gap:12px;align-items:flex-start;padding:13px 15px;margin-bottom:18px;border:1px solid #dcebe1;border-radius:10px;background:#f4faf6;color:#405149}.doc-form-intro-v22>i{margin-top:3px;color:#16824a}.doc-form-intro-v22 b,.doc-form-intro-v22 span{display:block}.doc-form-intro-v22 span{margin-top:3px;font-size:12px;color:#718078}
CSS);
?>
