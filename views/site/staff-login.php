<?php
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Masuk Petugas';
$this->registerMetaTag(['name'=>'robots','content'=>'noindex,nofollow,noarchive']);
?>
<section class="auth-page">
    <div class="auth-card">
        <span class="section-kicker">PORTAL PETUGAS</span>
        <h1>Admin & Verifikator.</h1>
        <p>Halaman ini khusus petugas Sumut Mengajar untuk mengelola batch dan proses seleksi peserta.</p>

        <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'username')->textInput(['placeholder' => 'Email atau username petugas'])->label('Email / Username') ?>
            <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Password']) ?>
            <?= $form->field($model, 'rememberMe')->checkbox()->label('Ingat saya') ?>
            <button class="btn-primary-gsm full" type="submit">Masuk ke Portal Petugas</button>
        <?php ActiveForm::end(); ?>

        <div class="auth-foot">
            Peserta? <a href="<?= Url::to(['/site/login']) ?>">Masuk Portal Peserta</a>
        </div>
    </div>
</section>
