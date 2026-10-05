<?php
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Masuk Peserta';
$this->registerMetaTag(['name'=>'robots','content'=>'noindex,nofollow,noarchive']);
?>
<section class="auth-page">
    <div class="auth-card">
        <span class="section-kicker">PORTAL PESERTA</span>
        <h1>Masuk dengan WhatsApp.</h1>
        <p>Gunakan nomor WhatsApp yang kamu daftarkan untuk mengisi formulir dan memantau proses seleksi.</p>

        <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'username')->textInput([
                'placeholder' => 'Contoh: 081234567890',
                'inputmode' => 'tel',
                'autocomplete' => 'tel',
            ])->label('Nomor WhatsApp') ?>
            <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Password', 'autocomplete' => 'current-password']) ?>
            <?= $form->field($model, 'rememberMe')->checkbox()->label('Ingat saya') ?>
            <button class="btn-primary-gsm full" type="submit">Masuk ke Portal Peserta</button>
        <?php ActiveForm::end(); ?>

        <div class="auth-foot">Belum punya akun? <a href="<?= Url::to(['/site/register']) ?>">Daftar peserta</a></div>
        <div class="auth-foot" style="margin-top:8px">Admin atau verifikator? <a href="<?= Url::to(['/site/staff-login']) ?>">Masuk Portal Petugas</a></div>
    </div>
</section>
