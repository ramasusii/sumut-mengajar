<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Tracking Pendaftaran';


$trackingCss = <<<'CSS'
.tracking-page{display:flex!important;flex-direction:column!important;gap:20px!important;width:100%!important}
.tracking-head{display:grid!important;grid-template-columns:minmax(0,1fr) 360px!important;gap:24px!important;align-items:end!important}
.tracking-back{display:inline-flex!important;margin-bottom:16px!important;color:#65756c!important;text-decoration:none!important;font-size:12px!important;font-weight:800!important}
.tracking-code{display:block!important;margin-bottom:8px!important;color:#16824a!important;font-size:11px!important;font-weight:900!important;letter-spacing:1.2px!important}
.tracking-head h1{margin:0 0 9px!important;font-size:36px!important;line-height:1.14!important;letter-spacing:-1.3px!important;color:#17251e!important}
.tracking-head>div:first-child>p{margin:0!important;max-width:760px!important;color:#6f7d75!important;font-size:13px!important;line-height:1.65!important}
.tracking-status-card{display:block!important;background:#fff!important;border:1px solid #e0e7e2!important;border-radius:20px!important;padding:20px!important;box-shadow:0 12px 30px rgba(25,60,44,.05)!important}
.tracking-status-card small{display:block!important;margin-bottom:8px!important;color:#859289!important;font-size:9px!important;font-weight:900!important;letter-spacing:1.2px!important}
.tracking-status-card strong{display:block!important;margin-bottom:7px!important;font-size:21px!important;line-height:1.25!important}
.tracking-status-card p{margin:0!important;color:#6e7c74!important;font-size:12px!important;line-height:1.55!important}
.tracking-status-card.success{background:#f1faf4!important;border-color:#cfe7d6!important}.tracking-status-card.success strong{color:#126d3d!important}
.tracking-status-card.progress{background:#fff9ec!important;border-color:#efdfbd!important}.tracking-status-card.progress strong{color:#986311!important}
.tracking-status-card.danger{background:#fff5f4!important;border-color:#efd6d2!important}.tracking-status-card.danger strong{color:#a1453e!important}
.tracking-panel{display:block!important;width:100%!important;background:#fff!important;border:1px solid #e0e7e2!important;border-radius:22px!important;padding:26px!important;box-shadow:0 10px 30px rgba(25,60,44,.03)!important}
.tracking-panel-head{display:flex!important;justify-content:space-between!important;align-items:end!important;gap:20px!important;margin-bottom:24px!important}
.tracking-panel-head span,.tracking-info-card>span,.tracking-next span,.tracking-result span{color:#16824a!important;font-size:9px!important;font-weight:900!important;letter-spacing:1.35px!important}
.tracking-panel-head h2{margin:4px 0 0!important;font-size:25px!important;line-height:1.2!important;letter-spacing:-.6px!important}
.tracking-progress-number{flex:0 0 auto!important;border-radius:999px!important;padding:8px 12px!important;background:#f2f5f3!important;color:#6f7d75!important;font-size:11px!important;font-weight:850!important}
.tracking-steps{display:grid!important;grid-template-columns:repeat(6,minmax(0,1fr))!important;gap:10px!important;width:100%!important}
.tracking-step{display:flex!important;align-items:center!important;gap:9px!important;min-width:0!important;min-height:66px!important;padding:11px!important;border:1px solid #ecefed!important;border-radius:15px!important;background:#f6f8f6!important}
.tracking-step-dot{display:grid!important;place-items:center!important;flex:0 0 31px!important;width:31px!important;height:31px!important;border-radius:50%!important;background:#e7ece8!important;color:#728078!important;font-size:11px!important;font-weight:900!important}
.tracking-step small{display:block!important;margin:0 0 2px!important;color:#96a099!important;font-size:7px!important;font-weight:900!important;letter-spacing:.65px!important}
.tracking-step strong{display:block!important;color:#243129!important;font-size:10px!important;font-weight:850!important;line-height:1.25!important}
.tracking-step.done{background:#eff8f1!important;border-color:#d7ecdd!important}.tracking-step.done .tracking-step-dot{background:#188b4b!important;color:#fff!important}.tracking-step.done strong{color:#176a3e!important}
.tracking-step.active{background:#fff6e6!important;border-color:#f1dfb9!important}.tracking-step.active .tracking-step-dot{background:#f28a24!important;color:#fff!important}.tracking-step.active strong{color:#8e5b12!important}
.tracking-result{display:flex!important;align-items:center!important;gap:18px!important;padding:24px!important;border-radius:21px!important}
.tracking-result-icon{display:grid!important;place-items:center!important;flex:0 0 54px!important;width:54px!important;height:54px!important;border-radius:16px!important;font-size:24px!important;font-weight:900!important}
.tracking-result h2{margin:5px 0 6px!important;font-size:23px!important;line-height:1.25!important;letter-spacing:-.5px!important}.tracking-result p{margin:0!important;font-size:12px!important;line-height:1.6!important}
.tracking-result-success{background:linear-gradient(135deg,#eaf8ee,#f8fcf9)!important;border:1px solid #d1ead8!important}.tracking-result-success .tracking-result-icon{background:#188b4b!important;color:#fff!important}.tracking-result-success p{color:#607168!important}
.tracking-result-muted{background:#fff7ec!important;border:1px solid #f1dfc7!important}.tracking-result-muted .tracking-result-icon{background:#f2a03b!important;color:#fff!important}.tracking-result-muted span{color:#b97218!important}.tracking-result-muted p{color:#766d60!important}
.tracking-info-grid{display:grid!important;grid-template-columns:1fr 1fr!important;gap:16px!important}
.tracking-info-card{display:block!important;background:#fff!important;border:1px solid #e0e7e2!important;border-radius:20px!important;padding:22px!important}
.tracking-info-card dl{margin:15px 0 0!important}.tracking-info-card dl>div{display:flex!important;justify-content:space-between!important;gap:18px!important;padding:11px 0!important;border-bottom:1px solid #edf1ee!important}.tracking-info-card dl>div:last-child{border-bottom:0!important}
.tracking-info-card dt{color:#7a8981!important;font-size:11px!important;font-weight:600!important}.tracking-info-card dd{margin:0!important;text-align:right!important;color:#233129!important;font-size:11px!important;font-weight:850!important}
.tracking-next{display:flex!important;align-items:center!important;justify-content:space-between!important;gap:24px!important;padding:22px 24px!important;border-radius:20px!important;background:#0e623a!important;color:#fff!important}
.tracking-next span{color:#cdeaa0!important}.tracking-next h3{max-width:760px!important;margin:5px 0 0!important;color:#fff!important;font-size:16px!important;line-height:1.45!important}
.tracking-next a{flex:0 0 auto!important;display:inline-flex!important;align-items:center!important;justify-content:center!important;padding:11px 15px!important;border-radius:999px!important;background:#fff!important;color:#0e623a!important;text-decoration:none!important;font-size:11px!important;font-weight:900!important}
@media(max-width:1100px){.tracking-steps{grid-template-columns:repeat(3,1fr)!important}}
@media(max-width:820px){.tracking-head,.tracking-info-grid{grid-template-columns:1fr!important}.tracking-next{align-items:flex-start!important;flex-direction:column!important}}
@media(max-width:560px){.tracking-head h1{font-size:28px!important}.tracking-steps{grid-template-columns:1fr 1fr!important}.tracking-panel{padding:18px!important}.tracking-panel-head{align-items:flex-start!important;flex-direction:column!important}}
CSS;

$this->registerCss($trackingCss);


$statusLabels = [
    'draft' => 'Belum Selesai',
    'submitted' => 'Menunggu Verifikasi',
    'revision_required' => 'Perlu Perbaikan Berkas',
    'verified' => 'Terverifikasi',
    'administration_pass' => 'Lolos Administrasi',
    'interview' => 'Tahap Wawancara',
    'interview_pass' => 'Lolos Wawancara',
    'final_pass' => 'Lolos Akhir',
    'rejected' => 'Belum Lolos',
];

$statusDescriptions = [
    'draft' => 'Lengkapi formulir pendaftaran sebelum batas waktu berakhir.',
    'submitted' => 'Pendaftaranmu sudah terkirim dan sedang menunggu verifikasi panitia.',
    'revision_required' => 'Panitia meminta perbaikan pada satu atau beberapa berkas. Buka formulir dan unggah berkas pengganti.',
    'verified' => 'Data dan berkasmu sudah diverifikasi. Pantau hasil seleksi administrasi di halaman ini.',
    'administration_pass' => 'Selamat, kamu lolos seleksi administrasi. Pantau informasi wawancara dari panitia.',
    'interview' => 'Kamu sedang berada pada tahap wawancara. Pastikan mengikuti jadwal dan ketentuan yang diberikan.',
    'interview_pass' => 'Tahap wawancara telah selesai. Pengumuman akhir akan tampil di halaman ini.',
    'final_pass' => 'Selamat! Kamu dinyatakan lolos sebagai Relawan Gerakan Sumut Mengajar pada batch ini.',
    'rejected' => 'Terima kasih telah mengikuti proses seleksi. Kamu belum lolos pada batch ini.',
];

$label = $statusLabels[$model->status] ?? 'Sedang Diproses';
$description = $statusDescriptions[$model->status] ?? 'Pendaftaranmu sedang diproses oleh panitia.';
$region = $model->batch && $model->batch->kabupatenKota
    ? $model->batch->kabupatenKota->label
    : 'Sumatera Utara';

$steps = [
    ['key' => 'account', 'label' => 'Akun'],
    ['key' => 'form', 'label' => 'Formulir'],
    ['key' => 'verify', 'label' => 'Verifikasi'],
    ['key' => 'admin', 'label' => 'Administrasi'],
    ['key' => 'interview', 'label' => 'Wawancara'],
    ['key' => 'result', 'label' => 'Pengumuman'],
];

$activeStep = match ($model->status) {
    'draft' => 2,
    'submitted', 'revision_required' => 3,
    'verified' => 4,
    'administration_pass', 'interview' => 5,
    'interview_pass', 'final_pass', 'rejected' => 6,
    default => 2,
};

$isFinished = in_array($model->status, ['final_pass', 'rejected'], true);
$resultClass = $model->status === 'final_pass' ? 'success' : ($model->status === 'rejected' ? 'danger' : 'progress');
?>

<div class="tracking-page">
    <div class="tracking-head">
        <div>
            <a class="tracking-back" href="<?= Url::to(['/applicant/dashboard']) ?>">← Kembali ke Dashboard</a>
            <span class="tracking-code"><?= Html::encode($model->application_code) ?></span>
            <h1><?= Html::encode($model->batch->title) ?></h1>
            <p>Pantau perkembangan pendaftaran dan setiap tahapan seleksi dari halaman ini.</p>
        </div>

        <div class="tracking-status-card <?= Html::encode($resultClass) ?>">
            <small>STATUS SAAT INI</small>
            <strong><?= Html::encode($label) ?></strong>
            <p><?= Html::encode($description) ?></p>
        </div>
    </div>

    <section class="tracking-panel">
        <div class="tracking-panel-head">
            <div>
                <span>PROGRES SELEKSI</span>
                <h2>Alur Rekrutmen</h2>
            </div>
            <div class="tracking-progress-number">
                <?= min($activeStep, count($steps)) ?>/<?= count($steps) ?> Tahap
            </div>
        </div>

        <div class="tracking-steps">
            <?php foreach ($steps as $index => $step): ?>
                <?php
                    $stepNumber = $index + 1;
                    $class = 'waiting';

                    if ($isFinished) {
                        $class = 'done';
                    } elseif ($stepNumber < $activeStep) {
                        $class = 'done';
                    } elseif ($stepNumber === $activeStep) {
                        $class = 'active';
                    }
                ?>
                <div class="tracking-step <?= $class ?>">
                    <div class="tracking-step-dot">
                        <?= $class === 'done' ? '✓' : $stepNumber ?>
                    </div>
                    <div>
                        <small>TAHAP <?= $stepNumber ?></small>
                        <strong><?= Html::encode($step['label']) ?></strong>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php if ($model->status === 'final_pass'): ?>
        <section class="tracking-result tracking-result-success">
            <div class="tracking-result-icon">✓</div>
            <div>
                <span>PENGUMUMAN AKHIR</span>
                <h2>Selamat, kamu lolos sebagai Relawan Sumut Mengajar!</h2>
                <p>Informasi pembekalan, keberangkatan, dan kebutuhan selanjutnya akan disampaikan oleh panitia melalui kanal resmi Sumut Mengajar.</p>
            </div>
        </section>
    <?php elseif ($model->status === 'rejected'): ?>
        <section class="tracking-result tracking-result-muted">
            <div class="tracking-result-icon">♡</div>
            <div>
                <span>PENGUMUMAN SELEKSI</span>
                <h2>Terima kasih sudah menjadi bagian dari proses ini.</h2>
                <p>Kamu belum lolos pada batch ini. Tetap pantau rekrutmen berikutnya dan terus bergerak bersama untuk pendidikan Sumatera Utara.</p>
            </div>
        </section>
    <?php endif; ?>

    <div class="tracking-info-grid">
        <section class="tracking-info-card">
            <span>INFORMASI PENDAFTARAN</span>
            <dl>
                <div><dt>Kode Pendaftaran</dt><dd><?= Html::encode($model->application_code) ?></dd></div>
                <div><dt>Batch</dt><dd>Batch <?= (int)$model->batch->batch_number ?></dd></div>
                <div><dt>Lokasi</dt><dd><?= Html::encode($region) ?></dd></div>
                <div><dt>Status</dt><dd><?= Html::encode($label) ?></dd></div>
            </dl>
        </section>

        <section class="tracking-info-card">
            <span>JADWAL BATCH</span>
            <dl>
                <div>
                    <dt>Pendaftaran</dt>
                    <dd>
                        <?= Yii::$app->formatter->asDate($model->batch->registration_start, 'php:d M Y') ?>
                        –
                        <?= Yii::$app->formatter->asDate($model->batch->registration_end, 'php:d M Y') ?>
                    </dd>
                </div>
                <div>
                    <dt>Wawancara</dt>
                    <dd><?= $model->batch->interview_date ? Yii::$app->formatter->asDate($model->batch->interview_date, 'php:d M Y') : 'Menunggu informasi' ?></dd>
                </div>
                <div>
                    <dt>Pembekalan</dt>
                    <dd><?= $model->batch->briefing_date ? Yii::$app->formatter->asDate($model->batch->briefing_date, 'php:d M Y') : 'Menunggu informasi' ?></dd>
                </div>
                <div>
                    <dt>Pengumuman</dt>
                    <dd><?= $model->batch->announcement_date ? Yii::$app->formatter->asDate($model->batch->announcement_date, 'php:d M Y') : 'Menunggu informasi' ?></dd>
                </div>
            </dl>
        </section>
    </div>

    <section class="tracking-next">
        <div>
            <span>LANGKAH BERIKUTNYA</span>
            <h3><?= Html::encode($description) ?></h3>
        </div>
        <?php if ($model->status === 'draft'): ?>
            <a href="<?= Url::to(['/applicant/form', 'id' => $model->id, 'step' => 1]) ?>">Isi Formulir Sekarang →</a>
        <?php elseif ($model->status === 'revision_required'): ?>
            <a href="<?= Url::to(['/applicant/form', 'id' => $model->id, 'step' => 3]) ?>">Perbaiki Berkas Sekarang →</a>
        <?php else: ?>
            <a href="<?= Url::to(['/recruitment/view', 'slug' => $model->batch->slug]) ?>">Lihat Informasi Batch →</a>
        <?php endif; ?>
    </section>
</div>
