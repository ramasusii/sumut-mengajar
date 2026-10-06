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

<section class="page-hero recruitment-detail gsm-recruitment-hero-v20">
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
            <div class="batch-closed-note">
                Tahap pendaftaran telah selesai. Pantau hasil melalui portal peserta atau pengumuman resmi Sumut Mengajar.
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section gsm-recruitment-detail-v20">
    <div class="container">
        <div class="gsm-detail-grid-v20">
            <article class="gsm-detail-card-v20 gsm-detail-card-v20--timeline">
                <div class="gsm-detail-card-v20__head">
                    <span class="gsm-detail-icon-v20"><i class="fa fa-calendar"></i></span>
                    <div>
                        <span class="gsm-detail-kicker-v20">JADWAL</span>
                        <h3>Timeline</h3>
                    </div>
                </div>

                <dl class="gsm-timeline-v20">
                    <div>
                        <dt>Lokasi Pengabdian</dt>
                        <dd><?= Html::encode($model->getLocationLabel()) ?></dd>
                    </div>
                    <div>
                        <dt>Pendaftaran</dt>
                        <dd>
                            <?= Yii::$app->formatter->asDate($model->registration_start) ?>
                            –
                            <?= Yii::$app->formatter->asDate($model->registration_end) ?>
                        </dd>
                    </div>
                    <div>
                        <dt>Wawancara</dt>
                        <dd><?= $model->interview_date ? Yii::$app->formatter->asDate($model->interview_date) : 'Akan diumumkan' ?></dd>
                    </div>
                    <div>
                        <dt>Pengumuman Akhir</dt>
                        <dd><?= $model->announcement_date ? Yii::$app->formatter->asDate($model->announcement_date) : 'Akan diumumkan' ?></dd>
                    </div>
                    <div>
                        <dt>Pembekalan</dt>
                        <dd><?= Html::encode($model->getBriefingPeriodLabel()) ?></dd>
                    </div>
                    <div>
                        <dt>Pengabdian</dt>
                        <dd><?= Html::encode($model->getServicePeriodLabel()) ?></dd>
                    </div>
                </dl>
            </article>

            <article class="gsm-detail-card-v20">
                <div class="gsm-detail-card-v20__head">
                    <span class="gsm-detail-icon-v20"><i class="fa fa-clipboard"></i></span>
                    <div>
                        <span class="gsm-detail-kicker-v20">SEBELUM MENDAFTAR</span>
                        <h3>Persyaratan</h3>
                    </div>
                </div>

                <div class="rich-text gsm-rich-text-v20">
                    <?= nl2br(Html::encode($model->requirements ?: 'Persyaratan akan diumumkan oleh panitia.')) ?>
                </div>
            </article>

            <article class="gsm-detail-card-v20">
                <div class="gsm-detail-card-v20__head">
                    <span class="gsm-detail-icon-v20"><i class="fa fa-gift"></i></span>
                    <div>
                        <span class="gsm-detail-kicker-v20">YANG DIDAPAT</span>
                        <h3>Benefit</h3>
                    </div>
                </div>

                <div class="rich-text gsm-rich-text-v20">
                    <?= nl2br(Html::encode($model->benefits ?: 'Benefit akan diumumkan oleh panitia.')) ?>
                </div>
            </article>
        </div>
    </div>
</section>

<?php
$this->registerCss(<<<'CSS'
.gsm-recruitment-hero-v20 .container{
    max-width:980px;
}
.gsm-recruitment-detail-v20{
    padding-top:64px;
    padding-bottom:86px;
}
.gsm-detail-grid-v20{
    display:grid !important;
    grid-template-columns:repeat(3,minmax(0,1fr)) !important;
    align-items:stretch !important;
    gap:22px !important;
    max-width:1120px;
    margin:0 auto;
}
.gsm-detail-card-v20{
    min-width:0;
    height:100%;
    padding:24px;
    border:1px solid #e3e9e5;
    border-radius:22px;
    background:#fff;
    box-shadow:0 14px 34px rgba(20,52,36,.055);
}
.gsm-detail-card-v20__head{
    display:flex;
    align-items:center;
    gap:12px;
    min-height:48px;
    margin-bottom:18px;
}
.gsm-detail-icon-v20{
    display:flex;
    align-items:center;
    justify-content:center;
    width:40px;
    height:40px;
    flex:0 0 40px;
    border-radius:12px;
    background:#eaf5ee;
    color:#0e623a;
}
.gsm-detail-kicker-v20{
    display:block;
    color:#829087;
    font-size:9px;
    font-weight:850;
    letter-spacing:.9px;
}
.gsm-detail-card-v20 h3{
    margin:2px 0 0;
    color:#223128;
    font-size:20px;
    line-height:1.2;
}
.gsm-timeline-v20{
    display:flex !important;
    flex-direction:column;
    gap:0 !important;
    margin:0;
}
.gsm-timeline-v20 > div{
    padding:10px 0;
    border-bottom:1px solid #edf1ee;
}
.gsm-timeline-v20 > div:last-child{
    border-bottom:0;
    padding-bottom:0;
}
.gsm-timeline-v20 dt{
    margin:0 0 3px !important;
    color:#829087 !important;
    font-size:10px !important;
    font-weight:800;
    letter-spacing:.45px;
    text-transform:uppercase;
}
.gsm-timeline-v20 dd{
    margin:0 !important;
    color:#243229;
    font-size:12px;
    font-weight:750;
    line-height:1.5;
}
.gsm-rich-text-v20{
    color:#526058;
    font-size:13px;
    line-height:1.75;
}
@media(max-width:860px){
    .gsm-detail-grid-v20{
        grid-template-columns:1fr !important;
        max-width:720px;
    }
}
@media(max-width:620px){
    .gsm-detail-card-v20{
        padding:20px;
    }
}
CSS);
?>
