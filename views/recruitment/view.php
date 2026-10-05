<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $model->title;

$statusLabels = [
    'open' => 'Pendaftaran Dibuka',
    'closed' => 'Pendaftaran Ditutup',
    'announced' => 'Pengumuman Terbit',
];

$isStaff = !Yii::$app->user->isGuest && (
    Yii::$app->user->can('superAdmin')
    || Yii::$app->user->can('adminGsm')
    || Yii::$app->user->can('reviewer')
);
$isApplicant = !Yii::$app->user->isGuest && Yii::$app->user->can('applicant');
?>
<section class="page-hero recruitment-detail">
    <div class="container">
        <span class="batch-status <?= Html::encode($model->status) ?>">
            <?= Html::encode($statusLabels[$model->status] ?? 'Informasi Rekrutmen') ?>
        </span>
        <h1><?= Html::encode($model->title) ?></h1>
        <p><?= Html::encode($model->description ?: 'Program pengabdian Gerakan Sumut Mengajar.') ?></p>

        <?php if ($model->isOpen()): ?>
            <?php if (Yii::$app->user->isGuest): ?>
                <a class="btn-primary-gsm" href="<?= Url::to(['/site/register']) ?>">Buat Akun & Daftar →</a>
            <?php elseif ($isApplicant): ?>
                <a class="btn-primary-gsm" href="<?= Url::to(['/applicant/apply', 'id' => $model->id]) ?>">Daftar Batch Ini →</a>
            <?php elseif ($isStaff): ?>
                <a class="btn-primary-gsm" href="<?= Url::to(['/admin-dashboard/index']) ?>">Kembali ke Dashboard Petugas →</a>
            <?php endif; ?>
        <?php elseif ($model->status === 'closed'): ?>
            <div class="batch-closed-note">Pendaftaran untuk batch ini sudah ditutup.</div>
        <?php elseif ($model->status === 'announced'): ?>
            <div class="batch-closed-note">Tahap pendaftaran telah selesai. Pantau hasil melalui portal peserta atau pengumuman resmi Sumut Mengajar.</div>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="container detail-grid">
        <article>
            <h3>Timeline</h3>
            <dl>
                <dt>Lokasi Pengabdian</dt>
                <dd><?= Html::encode($model->getLocationLabel()) ?></dd>
                <dt>Pendaftaran</dt>
                <dd><?= Yii::$app->formatter->asDate($model->registration_start) ?> – <?= Yii::$app->formatter->asDate($model->registration_end) ?></dd>
                <dt>Wawancara</dt>
                <dd><?= $model->interview_date ? Yii::$app->formatter->asDate($model->interview_date) : 'Akan diumumkan' ?></dd>
                <dt>Pembekalan</dt>
                <dd><?= $model->briefing_date ? Yii::$app->formatter->asDate($model->briefing_date) : 'Akan diumumkan' ?></dd>
                <dt>Pengabdian</dt>
                <dd>
                    <?= $model->activity_start ? Yii::$app->formatter->asDate($model->activity_start) : 'Akan diumumkan' ?>
                    <?= $model->activity_end ? ' – ' . Yii::$app->formatter->asDate($model->activity_end) : '' ?>
                </dd>
                <dt>Pengumuman</dt>
                <dd><?= $model->announcement_date ? Yii::$app->formatter->asDate($model->announcement_date) : 'Akan diumumkan' ?></dd>
            </dl>
        </article>
        <article><h3>Persyaratan</h3><div class="rich-text"><?= nl2br(Html::encode($model->requirements ?: 'Persyaratan akan diumumkan oleh panitia.')) ?></div></article>
        <article><h3>Benefit</h3><div class="rich-text"><?= nl2br(Html::encode($model->benefits ?: 'Benefit akan diumumkan oleh panitia.')) ?></div></article>
    </div>
</section>
