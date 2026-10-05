<?php
use app\models\Post;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$categoryItems = ArrayHelper::map($categories, 'id', 'name');
?>

<?php $form = ActiveForm::begin(['options'=>['enctype'=>'multipart/form-data']]); ?>

<div class="box box-success">
    <div class="box-body">
        <div class="row">
            <div class="col-md-8">
                <?= $form->field($model, 'title')->textInput(['maxlength'=>true])->label('Judul Artikel') ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'category_id')->dropDownList($categoryItems, ['prompt'=>'Pilih kategori'])->label('Kategori') ?>
            </div>
        </div>
<?= $form->field($model, 'excerpt')->textarea(['rows'=>3,'maxlength'=>600])->label('Ringkasan') ?>
        <?= $form->field($model, 'content')->textarea(['rows'=>15])->label('Isi Artikel') ?>

        <div class="row">
            <div class="col-md-6">
                <label>Gambar Utama Artikel</label>
                <input type="file" name="cover_upload" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                <p class="help-block">JPG/PNG/WEBP maksimal 5 MB.</p>
                <?php if($model->cover_image): ?>
                    <p><small>Cover saat ini: <?= Html::encode($model->cover_image) ?></small></p>
                <?php endif; ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'status')->dropDownList([
                    Post::STATUS_DRAFT => 'Draf',
                    Post::STATUS_PUBLISHED => 'Terbit',
                    Post::STATUS_ARCHIVED => 'Arsip',
                ])->label('Status Artikel') ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'published_at')->input('datetime-local', [
                    'value' => $model->published_at ? date('Y-m-d\TH:i', strtotime($model->published_at)) : '',
                ])->label('Waktu Tayang') ?>
            </div>
        </div>
    </div>
    <div class="box-footer">
        <?= Html::submitButton('<i class="fa fa-save"></i> Simpan Artikel', ['class'=>'btn btn-success']) ?>
        <?= Html::a('Kembali', ['index'], ['class'=>'btn btn-default']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>
