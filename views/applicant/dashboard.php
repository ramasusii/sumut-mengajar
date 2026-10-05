<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Dashboard Peserta';
$user = Yii::$app->user->identity;
$firstName = explode(' ', trim($user->nama ?: 'Relawan'))[0];

$statusLabels = [
    'draft' => 'Belum Selesai',
    'submitted' => 'Menunggu Verifikasi',
    'revision_required' => 'Perlu Perbaikan',
    'verified' => 'Terverifikasi',
    'administration_pass' => 'Lolos Administrasi',
    'interview' => 'Tahap Wawancara',
    'interview_pass' => 'Lolos Wawancara',
    'final_pass' => 'Lolos Akhir',
    'rejected' => 'Belum Lolos',
];
$statusClass = static fn($status) => match ($status) {
    'verified','administration_pass','interview_pass','final_pass' => 'success',
    'submitted','interview' => 'progress',
    'revision_required' => 'warning',
    'rejected' => 'danger',
    default => 'neutral',
};
$applicationByBatch = [];
foreach ($applications as $application) {
    $applicationByBatch[(int)$application->batch_id] = $application;
}
?>

<section class="portal-v2-welcome">
    <div class="portal-v2-welcome-copy">
        <span class="portal-v2-eyebrow">PORTAL PESERTA</span>
        <h1>Halo, <?= Html::encode($firstName) ?>! <span>👋</span></h1>
        <p>Pantau pendaftaran, perbaikan berkas, jadwal, dan hasil seleksi dari satu tempat.</p>
    </div>
    <div class="portal-v2-welcome-badge"><span>GERAKAN SUMUT MENGAJAR</span><strong>Sekali Mengabdi,<br>Selamanya Menginspirasi.</strong></div>
</section>

<section class="portal-v2-stats">
    <article><span class="portal-v2-stat-icon green">✓</span><div><strong><?= count($applications) ?></strong><small>Pendaftaran Saya</small></div></article>
    <article><span class="portal-v2-stat-icon orange">◎</span><div><strong><?= count($openBatches) ?></strong><small>Batch Sedang Dibuka</small></div></article>
    <article><span class="portal-v2-stat-icon navy">33</span><div><strong>Sumatera Utara</strong><small>Wilayah Gerakan</small></div></article>
</section>

<?php if ($applications): ?>
<section id="pendaftaran-saya" class="portal-v2-section">
    <div class="portal-v2-section-head"><div><span>AKTIVITAS SAYA</span><h2>Pendaftaran Saya</h2></div><a href="<?= Url::to(['/recruitment/index']) ?>">Lihat semua rekrutmen →</a></div>
    <div class="portal-v2-application-list">
        <?php foreach ($applications as $app): ?>
            <?php $region = $app->batch->kabupatenKota ? $app->batch->kabupatenKota->label : 'Sumatera Utara'; ?>
            <article class="portal-v2-application-card">
                <div class="portal-v2-app-main">
                    <div class="portal-v2-app-code"><?= Html::encode($app->application_code) ?></div>
                    <h3><?= Html::encode($app->batch->title) ?></h3>
                    <div class="portal-v2-app-meta"><span>Batch <?= (int)$app->batch->batch_number ?></span><span><?= Html::encode($region) ?></span></div>
                </div>
                <div class="portal-v2-app-action">
                    <span class="portal-v2-status <?= $statusClass($app->status) ?>"><?= Html::encode($statusLabels[$app->status] ?? 'Sedang Diproses') ?></span>
                    <?php if ($app->status === 'draft'): ?>
                        <a href="<?= Url::to(['/applicant/form', 'id' => $app->id, 'step' => 1]) ?>">Lanjutkan Formulir →</a>
                    <?php elseif ($app->status === 'revision_required'): ?>
                        <a href="<?= Url::to(['/applicant/form', 'id' => $app->id, 'step' => 3]) ?>">Perbaiki Berkas →</a>
                    <?php else: ?>
                        <a href="<?= Url::to(['/applicant/application', 'id' => $app->id]) ?>">Lacak Progres →</a>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section class="portal-v2-section">
    <div class="portal-v2-section-head"><div><span>REKRUTMEN</span><h2>Batch yang Sedang Dibuka</h2></div><a href="<?= Url::to(['/recruitment/index']) ?>">Lihat halaman rekrutmen →</a></div>
    <?php if (!$openBatches): ?>
        <div class="portal-v2-empty"><div class="portal-v2-empty-icon">✦</div><div><h3>Belum ada batch yang sedang dibuka.</h3><p>Informasi rekrutmen aktif akan tampil di sini.</p></div><a href="<?= Url::to(['/site/index']) ?>">Kembali ke Website</a></div>
    <?php else: ?>
        <div class="portal-v2-batch-grid">
            <?php foreach ($openBatches as $batch): ?>
                <?php $existing = $applicationByBatch[(int)$batch->id] ?? null; $region = $batch->kabupatenKota ? $batch->kabupatenKota->label : 'Sumatera Utara'; ?>
                <article class="portal-v2-batch-card">
                    <div class="portal-v2-batch-top"><span class="portal-v2-open-dot"><i></i> PENDAFTARAN DIBUKA</span><span>Batch <?= (int)$batch->batch_number ?></span></div>
                    <h3><?= Html::encode($batch->title) ?></h3>
                    <p><?= Html::encode($batch->description ?: 'Kesempatan belajar, mengabdi, dan tumbuh bersama Sumut Mengajar.') ?></p>
                    <div class="portal-v2-batch-info"><span><b>Lokasi</b><?= Html::encode($region) ?></span><span><b>Ditutup</b><?= Yii::$app->formatter->asDate($batch->registration_end, 'php:d M Y') ?></span></div>
                    <?php if ($existing): ?>
                        <div class="portal-v2-registered-actions">
                            <span class="portal-v2-primary portal-v2-primary-disabled"><span>✓ Sudah Terdaftar</span><span>Batch <?= (int)$batch->batch_number ?></span></span>
                            <?php if ($existing->status === 'draft'): ?>
                                <a class="portal-v2-track-link" href="<?= Url::to(['/applicant/form', 'id' => $existing->id, 'step' => 1]) ?>">Lanjutkan Formulir →</a>
                            <?php elseif ($existing->status === 'revision_required'): ?>
                                <a class="portal-v2-track-link" href="<?= Url::to(['/applicant/form', 'id' => $existing->id, 'step' => 3]) ?>">Perbaiki Berkas →</a>
                            <?php else: ?>
                                <a class="portal-v2-track-link" href="<?= Url::to(['/applicant/application', 'id' => $existing->id]) ?>">Lacak Progres →</a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <a class="portal-v2-primary" href="<?= Url::to(['/applicant/apply', 'id' => $batch->id]) ?>">Daftar Batch Ini <span>→</span></a>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
