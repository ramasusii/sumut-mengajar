<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Cek Status Pendaftaran';

$statusLabels = [
    'draft' => 'Formulir Belum Selesai',
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
    'draft' => 'Formulir pendaftaran belum dikirim.',
    'submitted' => 'Pendaftaran sudah diterima dan sedang diperiksa oleh panitia.',
    'revision_required' => 'Ada berkas yang perlu diperbaiki. Masuk ke Portal Peserta untuk melihat catatan panitia.',
    'verified' => 'Data dan berkas pendaftaran telah diverifikasi.',
    'administration_pass' => 'Peserta dinyatakan lolos seleksi administrasi.',
    'interview' => 'Peserta sedang mengikuti tahap wawancara.',
    'interview_pass' => 'Peserta dinyatakan lolos tahap wawancara.',
    'final_pass' => 'Selamat! Peserta dinyatakan lolos akhir sebagai Relawan Sumut Mengajar.',
    'rejected' => 'Terima kasih telah mengikuti proses seleksi. Peserta belum lolos pada batch ini.',
];

$steps = [
    1 => 'Akun',
    2 => 'Formulir',
    3 => 'Verifikasi',
    4 => 'Administrasi',
    5 => 'Wawancara',
    6 => 'Pengumuman',
];

$activeStep = static function (string $status): int {
    return match ($status) {
        'draft' => 2,
        'submitted', 'revision_required' => 3,
        'verified' => 4,
        'administration_pass', 'interview' => 5,
        'interview_pass', 'final_pass' => 6,
        'rejected' => 6,
        default => 2,
    };
};

$this->registerCss(<<<'CSS'
.check-status-v9{padding:58px 20px 90px;background:linear-gradient(180deg,#f6faf7,#fff)}
.check-status-v9__wrap{width:min(1020px,100%);margin:0 auto}
.check-status-v9__head{text-align:center;max-width:720px;margin:0 auto 28px}
.check-status-v9__head span{font-size:10px;font-weight:900;letter-spacing:1.35px;color:#16824a}
.check-status-v9__head h1{font-size:40px;line-height:1.1;letter-spacing:-1px;margin:8px 0 10px;color:#17251e}
.check-status-v9__head p{font-size:13px;line-height:1.7;color:#708077}
.check-status-v9__search{max-width:720px;margin:0 auto;background:#fff;border:1px solid #dfe7e1;border-radius:22px;padding:19px;box-shadow:0 18px 45px rgba(20,60,40,.07)}
.check-status-v9__form{display:flex;gap:10px}
.check-status-v9__input{flex:1;height:50px;border-radius:999px;border:1px solid #d7e2da;padding:0 18px;outline:none;text-transform:uppercase;font-size:12px;font-weight:750}
.check-status-v9__input:focus{border-color:#6faa83;box-shadow:0 0 0 3px rgba(22,130,74,.08)}
.check-status-v9__button{height:50px;border:0;border-radius:999px;background:#0f6b3f;color:#fff;padding:0 22px;font-size:11px;font-weight:900;cursor:pointer}
.check-status-v9__error{margin-top:10px;padding:10px 12px;border-radius:11px;background:#fff1ef;color:#a14339;font-size:11px}
.check-status-v9__hint{margin-top:9px;font-size:10px;color:#87928c;text-align:center}
.check-status-v9__result{margin-top:26px;display:grid;gap:16px}
.check-status-v9__status{background:#fff;border:1px solid #dfe7e1;border-radius:22px;padding:24px;display:grid;grid-template-columns:1fr auto;gap:20px;align-items:center}
.check-status-v9__status small{display:block;color:#8a958f;font-size:9px;font-weight:900;letter-spacing:1.15px}
.check-status-v9__status h2{margin:5px 0 6px;font-size:27px;color:#17251e}
.check-status-v9__status p{margin:0;color:#6f7d75;font-size:12px;line-height:1.6}
.check-status-v9__code{border-radius:999px;padding:9px 13px;background:#eef6f0;color:#14663c;font-size:10px;font-weight:900;white-space:nowrap}
.check-status-v9__steps{background:#fff;border:1px solid #dfe7e1;border-radius:22px;padding:22px;display:grid;grid-template-columns:repeat(6,1fr);gap:9px}
.check-status-v9__step{padding:12px;border-radius:14px;background:#f4f6f4;border:1px solid #e8ece9;min-width:0}
.check-status-v9__num{width:29px;height:29px;border-radius:50%;display:grid;place-items:center;background:#e4e9e5;color:#718077;font-size:10px;font-weight:900;margin-bottom:8px}
.check-status-v9__step small{font-size:7px;color:#98a19b;font-weight:900;letter-spacing:.7px}
.check-status-v9__step strong{display:block;font-size:10px;color:#27342c;margin-top:2px}
.check-status-v9__step.done{background:#edf8f0;border-color:#d5eadb}
.check-status-v9__step.done .check-status-v9__num{background:#188b4b;color:#fff}
.check-status-v9__step.active{background:#fff5e5;border-color:#f0ddb8}
.check-status-v9__step.active .check-status-v9__num{background:#f28a24;color:#fff}
.check-status-v9__info{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.check-status-v9__card{background:#fff;border:1px solid #dfe7e1;border-radius:20px;padding:20px}
.check-status-v9__card>span{font-size:9px;color:#16824a;font-weight:900;letter-spacing:1.15px}
.check-status-v9__row{display:flex;justify-content:space-between;gap:18px;border-bottom:1px solid #edf1ee;padding:10px 0;font-size:11px}
.check-status-v9__row:last-child{border-bottom:0}
.check-status-v9__row span{color:#7b8981}
.check-status-v9__row b{text-align:right;color:#27342c}
.check-status-v9__notice{border-radius:20px;padding:22px;background:#eaf8ee;border:1px solid #d1ead8}
.check-status-v9__notice h3{margin:5px 0;color:#126d3d;font-size:20px}
.check-status-v9__notice p{margin:0;color:#607168;font-size:11px;line-height:1.6}
.check-status-v9__notice.muted{background:#fff7ec;border-color:#f1dfc7}
.check-status-v9__notice.muted h3{color:#995d12}
.check-status-v9__privacy{text-align:center;margin-top:18px;color:#88938d;font-size:10px;line-height:1.6}
.check-status-v9__privacy a{color:#0e623a;font-weight:850;text-decoration:none}
@media(max-width:900px){.check-status-v9__steps{grid-template-columns:repeat(3,1fr)}}
@media(max-width:650px){
    .check-status-v9__head h1{font-size:31px}
    .check-status-v9__form{flex-direction:column}
    .check-status-v9__button{width:100%}
    .check-status-v9__status{grid-template-columns:1fr}
    .check-status-v9__info{grid-template-columns:1fr}
    .check-status-v9__steps{grid-template-columns:1fr 1fr}
}
CSS);
?>

<section class="check-status-v9">
    <div class="check-status-v9__wrap">
        <div class="check-status-v9__head">
            <span>CEK STATUS PENDAFTARAN</span>
            <h1>Pantau proses seleksi dengan kode pendaftaran.</h1>
            <p>
                Masukkan kode pendaftaran untuk melihat perkembangan seleksi.
                Tidak perlu masuk ke akun peserta.
            </p>
        </div>

        <div class="check-status-v9__search">
            <form
                class="check-status-v9__form"
                action="<?= Url::to(['/site/track']) ?>"
                method="get"
            >
                <input
                    class="check-status-v9__input"
                    type="text"
                    name="kode"
                    value="<?= Html::encode($model->application_code) ?>"
                    maxlength="40"
                    autocomplete="off"
                    placeholder="Contoh: GSM-2026-01-00006"
                    aria-label="Kode pendaftaran"
                    required
                >

                <button class="check-status-v9__button" type="submit">
                    Cek Status →
                </button>
            </form>

            <?php if ($model->hasErrors('application_code')): ?>
                <div class="check-status-v9__error">
                    <?= Html::encode($model->getFirstError('application_code')) ?>
                </div>
            <?php endif; ?>

            <div class="check-status-v9__hint">
                Kode pendaftaran tersedia di Portal Peserta setelah memilih batch.
            </div>
        </div>

        <?php if ($application): ?>
            <?php
            $status = $application->status;
            $label = $statusLabels[$status] ?? 'Sedang Diproses';
            $description = $statusDescriptions[$status] ?? 'Pendaftaran sedang diproses oleh panitia.';
            $stepNow = $activeStep($status);

            $region = $application->batch->kabupatenKota
                ? $application->batch->kabupatenKota->label
                : 'Sumatera Utara';
            ?>

            <div class="check-status-v9__result">
                <section class="check-status-v9__status">
                    <div>
                        <small>STATUS SAAT INI</small>
                        <h2><?= Html::encode($label) ?></h2>
                        <p><?= Html::encode($description) ?></p>
                    </div>

                    <div class="check-status-v9__code">
                        <?= Html::encode($application->application_code) ?>
                    </div>
                </section>

                <?php if ($status !== 'rejected'): ?>
                    <section class="check-status-v9__steps">
                        <?php foreach ($steps as $number => $step): ?>
                            <?php
                            $class = '';
                            if ($status === 'final_pass' || $number < $stepNow) {
                                $class = 'done';
                            } elseif ($number === $stepNow) {
                                $class = 'active';
                            }
                            ?>

                            <div class="check-status-v9__step <?= $class ?>">
                                <div class="check-status-v9__num">
                                    <?= $class === 'done' ? '✓' : $number ?>
                                </div>
                                <small>TAHAP <?= $number ?></small>
                                <strong><?= Html::encode($step) ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </section>
                <?php endif; ?>

                <?php if ($status === 'final_pass'): ?>
                    <section class="check-status-v9__notice">
                        <span>HASIL AKHIR</span>
                        <h3>Selamat, kamu lolos sebagai Relawan Sumut Mengajar!</h3>
                        <p>
                            Informasi pembekalan dan keberangkatan berikutnya
                            akan disampaikan melalui kanal resmi Sumut Mengajar.
                        </p>
                    </section>
                <?php elseif ($status === 'rejected'): ?>
                    <section class="check-status-v9__notice muted">
                        <span>HASIL SELEKSI</span>
                        <h3>Terima kasih sudah mengikuti proses seleksi.</h3>
                        <p>
                            Pendaftaran ini belum lolos pada batch tersebut.
                            Pantau kesempatan pengabdian berikutnya di website Sumut Mengajar.
                        </p>
                    </section>
                <?php endif; ?>

                <div class="check-status-v9__info">
                    <section class="check-status-v9__card">
                        <span>INFORMASI PENDAFTARAN</span>

                        <div class="check-status-v9__row">
                            <span>Kode Pendaftaran</span>
                            <b><?= Html::encode($application->application_code) ?></b>
                        </div>

                        <div class="check-status-v9__row">
                            <span>Batch</span>
                            <b>Batch <?= (int)$application->batch->batch_number ?></b>
                        </div>

                        <div class="check-status-v9__row">
                            <span>Lokasi Pengabdian</span>
                            <b><?= Html::encode($region) ?></b>
                        </div>

                        <div class="check-status-v9__row">
                            <span>Status</span>
                            <b><?= Html::encode($label) ?></b>
                        </div>
                    </section>

                    <section class="check-status-v9__card">
                        <span>JADWAL SELEKSI</span>

                        <div class="check-status-v9__row">
                            <span>Wawancara</span>
                            <b>
                                <?= $application->batch->interview_date
                                    ? Yii::$app->formatter->asDate($application->batch->interview_date)
                                    : 'Akan diinformasikan' ?>
                            </b>
                        </div>

                        <div class="check-status-v9__row">
                            <span>Pembekalan</span>
                            <b>
                                <?= $application->batch->briefing_date
                                    ? Yii::$app->formatter->asDate($application->batch->briefing_date)
                                    : 'Akan diinformasikan' ?>
                            </b>
                        </div>

                        <div class="check-status-v9__row">
                            <span>Pengumuman Akhir</span>
                            <b>
                                <?= $application->batch->announcement_date
                                    ? Yii::$app->formatter->asDate($application->batch->announcement_date)
                                    : 'Akan diinformasikan' ?>
                            </b>
                        </div>
                    </section>
                </div>

                <?php if ($status === 'revision_required'): ?>
                    <div class="check-status-v9__privacy">
                        Masuk ke
                        <a href="<?= Url::to(['/site/login']) ?>">Portal Peserta</a>
                        untuk melihat catatan perbaikan berkas.
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="check-status-v9__privacy">
            Data pribadi dan dokumen peserta tidak ditampilkan di halaman ini.
        </div>
    </div>
</section>
