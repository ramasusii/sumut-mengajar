<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Rekrutmen';

$statusLabels = [
    'open' => 'Pendaftaran Dibuka',
    'closed' => 'Pendaftaran Ditutup',
    'announced' => 'Pengumuman Terbit',
];
?>

<section class="page-hero">
    <div class="container">
        <span class="section-kicker">REKRUTMEN</span>
        <h1>Temukan batch pengabdianmu.</h1>
        <p>Satu portal untuk pendaftaran, verifikasi, seleksi, wawancara, hingga pengumuman.</p>
    </div>
</section>

<section class="section gsm-recruitment-list-v20">
    <div class="container">
        <?php if (!$batches): ?>
            <div class="empty-card">
                <h3>Belum ada batch yang dipublikasikan.</h3>
                <p>Informasi rekrutmen berikutnya akan tampil di halaman ini.</p>
            </div>
        <?php else: ?>
            <div class="gsm-batch-grid-v20">
                <?php foreach ($batches as $batch): ?>
                    <article class="gsm-batch-card-v20">
                        <div class="gsm-batch-card-v20__top">
                            <span class="batch-status <?= Html::encode($batch->status) ?>">
                                <?= Html::encode($statusLabels[$batch->status] ?? 'Informasi Rekrutmen') ?>
                            </span>

                            <h2><?= Html::encode($batch->title) ?></h2>

                            <p class="gsm-batch-card-v20__desc">
                                <?= Html::encode($batch->description ?: 'Program pengabdian Sumut Mengajar.') ?>
                            </p>
                        </div>

                        <div class="gsm-batch-card-v20__meta">
                            <div class="gsm-batch-meta-v20">
                                <span class="gsm-batch-meta-v20__label">Batch</span>
                                <strong><?= (int) $batch->batch_number ?></strong>
                            </div>

                            <div class="gsm-batch-meta-v20 gsm-batch-meta-v20--wide">
                                <span class="gsm-batch-meta-v20__label">Lokasi Pengabdian</span>
                                <strong><?= Html::encode($batch->getLocationLabel()) ?></strong>
                            </div>
                        </div>

                        <div class="gsm-batch-card-v20__footer">
                            <div class="gsm-batch-card-v20__period">
                                <?php if ($batch->registration_start && $batch->registration_end): ?>
                                    Pendaftaran <?= Yii::$app->formatter->asDate($batch->registration_start, 'php:d M') ?>
                                    – <?= Yii::$app->formatter->asDate($batch->registration_end, 'php:d M Y') ?>
                                <?php else: ?>
                                    Jadwal pendaftaran akan diumumkan.
                                <?php endif; ?>
                            </div>

                            <a
                                class="gsm-detail-button-v20"
                                href="<?= Url::to(['/recruitment/view', 'slug' => $batch->slug]) ?>"
                            >
                                Lihat Detail <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
$this->registerCss(<<<'CSS'
.gsm-recruitment-list-v20{
    padding-top:70px;
}
.gsm-batch-grid-v20{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:24px;
    max-width:1040px;
    margin:0 auto;
}
.gsm-batch-card-v20{
    display:flex;
    flex-direction:column;
    min-height:330px;
    padding:28px;
    border:1px solid #e3e9e5;
    border-radius:24px;
    background:#fff;
    box-shadow:0 16px 38px rgba(20,52,36,.055);
}
.gsm-batch-card-v20__top h2{
    margin:14px 0 10px;
    font-size:27px;
    line-height:1.14;
    letter-spacing:-.8px;
}
.gsm-batch-card-v20__desc{
    margin:0;
    color:#68756e;
    line-height:1.65;
}
.gsm-batch-card-v20__meta{
    display:grid;
    grid-template-columns:110px 1fr;
    gap:10px;
    margin-top:24px;
}
.gsm-batch-meta-v20{
    padding:12px 14px;
    border-radius:14px;
    background:#f7faf8;
    border:1px solid #ebf0ed;
}
.gsm-batch-meta-v20__label{
    display:block;
    margin-bottom:4px;
    color:#819087;
    font-size:10px;
    font-weight:800;
    letter-spacing:.7px;
    text-transform:uppercase;
}
.gsm-batch-meta-v20 strong{
    display:block;
    color:#26372d;
    font-size:12px;
    line-height:1.45;
}
.gsm-batch-card-v20__footer{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:14px;
    margin-top:auto;
    padding-top:20px;
    border-top:1px solid #e8ede9;
}
.gsm-batch-card-v20__period{
    color:#78857d;
    font-size:11px;
    line-height:1.45;
}
.gsm-detail-button-v20{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    flex:0 0 auto;
    padding:11px 16px;
    border-radius:999px;
    background:#0e623a;
    color:#fff !important;
    text-decoration:none !important;
    font-size:12px;
    font-weight:800;
    box-shadow:0 9px 20px rgba(14,98,58,.18);
    transition:transform .18s ease, box-shadow .18s ease, background .18s ease;
}
.gsm-detail-button-v20:hover,
.gsm-detail-button-v20:focus{
    background:#0b5532;
    transform:translateY(-1px);
    box-shadow:0 12px 24px rgba(14,98,58,.24);
}
@media(max-width:900px){
    .gsm-batch-grid-v20{
        grid-template-columns:1fr;
    }
}
@media(max-width:620px){
    .gsm-batch-card-v20{
        padding:22px;
    }
    .gsm-batch-card-v20__meta{
        grid-template-columns:1fr;
    }
    .gsm-batch-card-v20__footer{
        align-items:flex-start;
        flex-direction:column;
    }
    .gsm-detail-button-v20{
        width:100%;
    }
}
CSS);
?>
