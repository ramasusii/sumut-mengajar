<?php
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Daftar Peserta';
$this->registerMetaTag(['name'=>'robots','content'=>'noindex,nofollow,noarchive']);
?>
<section class="auth-page">
    <div class="auth-card wide">
        <span class="section-kicker">AKUN PESERTA</span>
        <h1>Mulai perjalanan pengabdianmu.</h1>
        <p>Satu nomor WhatsApp untuk mengikuti rekrutmen Sumut Mengajar dan memantau seluruh proses seleksi.</p>

        <?php $form = ActiveForm::begin(); ?>
        <div class="form-grid">
            <?= $form->field($model, 'nama')->textInput(['placeholder' => 'Nama lengkap', 'autocomplete' => 'name']) ?>
            <?= $form->field($model, 'whatsapp')->textInput([
                'placeholder' => '081234567890',
                'inputmode' => 'tel',
                'autocomplete' => 'tel',
            ])->label('Nomor WhatsApp Aktif') ?>
            <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Minimal 8 karakter', 'autocomplete' => 'new-password']) ?>
            <?= $form->field($model, 'password_repeat')->passwordInput(['placeholder' => 'Ulangi password', 'autocomplete' => 'new-password']) ?>
        </div>
        <button class="btn-primary-gsm full" type="submit">Buat Akun Peserta</button>
        <?php ActiveForm::end(); ?>

        <div class="auth-foot">Sudah punya akun? <a href="<?= Url::to(['/site/login']) ?>">Masuk</a></div>
    </div>
</section>
