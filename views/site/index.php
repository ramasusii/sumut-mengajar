<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Gerakan Sumut Mengajar';

$imgBase = Yii::$app->request->baseUrl . '/web/assets-guest/img';

$publicUrl = static function (?string $path): ?string {
    if (!$path) {
        return null;
    }

    return Yii::$app->request->baseUrl . '/' . ltrim($path, '/');
};

/*
 * Critical homepage layout.
 * Diregistrasikan langsung di halaman supaya perbaikan alignment tidak
 * tertahan cache asset lama.
 */
$this->registerCss(<<<'CSS'
/* ============================================================
   HOMEPAGE FORCE ALIGN V6
   Unique classes + explicit desktop/mobile breakpoints.
   ============================================================ */

.gsm-home-v6-marker{display:none!important}

/* Pair layout base */
.gsm-pair-v6{
    width:100%!important;
    margin:0!important;
    padding:0!important;
    box-sizing:border-box!important;
}

.gsm-pair-v6__media,
.gsm-pair-v6__copy{
    box-sizing:border-box!important;
    min-width:0!important;
    margin:0!important;
}

.gsm-pair-v6__media{
    overflow:hidden!important;
}

.gsm-pair-v6__media img{
    display:block!important;
    width:100%!important;
    max-width:100%!important;
    margin:0!important;
}

/* DESKTOP/TABLET LANDSCAPE:
   selalu satu baris selama viewport > 768px */
@media (min-width:769px){
    .gsm-pair-v6{
        display:flex!important;
        flex-direction:row!important;
        flex-wrap:nowrap!important;
        align-items:center!important;
        justify-content:space-between!important;
        gap:clamp(32px,5vw,72px)!important;
    }

    .gsm-pair-v6__media,
    .gsm-pair-v6__copy{
        display:block!important;
        flex:1 1 0!important;
        width:calc(50% - 36px)!important;
        max-width:calc(50% - 16px)!important;
    }

    /* Tentang: gambar kiri, teks kanan */
    .gsm-pair-v6--about .gsm-pair-v6__media{
        order:1!important;
    }

    .gsm-pair-v6--about .gsm-pair-v6__copy{
        order:2!important;
    }

    .gsm-pair-v6--about .gsm-pair-v6__media img{
        height:clamp(360px,33vw,480px)!important;
        object-fit:cover!important;
        object-position:center!important;
        border-radius:28px!important;
    }

    /* Rekrutmen: teks kiri, poster kanan */
    .gsm-pair-v6--recruit .gsm-pair-v6__copy{
        order:1!important;
        flex:1.08 1 0!important;
    }

    .gsm-pair-v6--recruit .gsm-pair-v6__media{
        order:2!important;
        flex:.92 1 0!important;
        display:flex!important;
        align-items:center!important;
        justify-content:center!important;
        overflow:visible!important;
    }

    .gsm-pair-v6--recruit .gsm-pair-v6__media img{
        width:min(100%,430px)!important;
        height:auto!important;
        max-height:570px!important;
        object-fit:contain!important;
        border-radius:26px!important;
    }
}

/* MOBILE:
   baru menjadi atas-bawah di <= 768px */
@media (max-width:768px){
    .gsm-pair-v6{
        display:flex!important;
        flex-direction:column!important;
        flex-wrap:nowrap!important;
        align-items:stretch!important;
        gap:26px!important;
    }

    .gsm-pair-v6__media,
    .gsm-pair-v6__copy{
        flex:0 0 auto!important;
        width:100%!important;
        max-width:100%!important;
    }

    .gsm-pair-v6--about .gsm-pair-v6__media{
        order:1!important;
    }

    .gsm-pair-v6--about .gsm-pair-v6__copy{
        order:2!important;
    }

    .gsm-pair-v6--about .gsm-pair-v6__media img{
        height:clamp(260px,72vw,380px)!important;
        object-fit:cover!important;
        object-position:center!important;
        border-radius:20px!important;
    }

    .gsm-pair-v6--recruit .gsm-pair-v6__copy{
        order:1!important;
    }

    .gsm-pair-v6--recruit .gsm-pair-v6__media{
        order:2!important;
        display:flex!important;
        justify-content:center!important;
    }

    .gsm-pair-v6--recruit .gsm-pair-v6__media img{
        width:min(100%,390px)!important;
        height:auto!important;
        max-height:520px!important;
        object-fit:contain!important;
        border-radius:22px!important;
    }
}

/* Hero: gambar saja bila tidak ada konten */
.gsm-hero-slide:not(.has-content) .gsm-hero-overlay,
.gsm-hero-slide:not(.has-content) .gsm-hero-content{
    display:none!important;
}

.gsm-hero-slide:not(.has-content) .gsm-hero-media img{
    filter:none!important;
}

/* Desktop hero follows banner ratio */
@media (min-width:769px){
    .gsm-hero-slider{
        width:100%!important;
        height:auto!important;
        min-height:0!important;
        aspect-ratio:1920/850!important;
    }
}

/* Mobile hero portrait ratio */
@media (max-width:768px){
    .gsm-hero-slider{
        width:100%!important;
        height:auto!important;
        min-height:0!important;
        aspect-ratio:4/5!important;
    }
}

/* CEK STATUS PENDAFTARAN V9 */
.gsm-track-search-v9{
    background:#fff!important;
    border-bottom:1px solid #e4ebe6!important;
    padding:22px 0!important;
}
.gsm-track-search-v9__inner{
    display:grid!important;
    grid-template-columns:auto minmax(240px,.8fr) minmax(360px,1.2fr)!important;
    gap:18px!important;
    align-items:center!important;
    padding:18px 22px!important;
    border-radius:20px!important;
    background:linear-gradient(135deg,#f3faf5 0%,#fff8e9 100%)!important;
    border:1px solid #dce9df!important;
    box-shadow:0 12px 34px rgba(20,70,43,.05)!important;
}
.gsm-track-search-v9__icon{
    width:50px!important;
    height:50px!important;
    border-radius:15px!important;
    display:grid!important;
    place-items:center!important;
    background:#0f6b3f!important;
    color:#fff!important;
    font-size:22px!important;
    font-weight:900!important;
}
.gsm-track-search-v9__copy small{
    display:block!important;
    margin-bottom:4px!important;
    color:#16824a!important;
    font-size:9px!important;
    line-height:1.2!important;
    font-weight:900!important;
    letter-spacing:1.25px!important;
}
.gsm-track-search-v9__copy h3{
    margin:0 0 3px!important;
    color:#17251e!important;
    font-size:19px!important;
    line-height:1.25!important;
}
.gsm-track-search-v9__copy p{
    margin:0!important;
    color:#6d7b73!important;
    font-size:11px!important;
    line-height:1.5!important;
}
.gsm-track-search-v9__form{
    display:flex!important;
    align-items:center!important;
    gap:9px!important;
    width:100%!important;
    margin:0!important;
}
.gsm-track-search-v9__input{
    flex:1 1 auto!important;
    width:100%!important;
    height:46px!important;
    border:1px solid #d7e2da!important;
    border-radius:999px!important;
    background:#fff!important;
    padding:0 18px!important;
    outline:none!important;
    color:#243129!important;
    font-size:12px!important;
    font-weight:750!important;
    text-transform:uppercase!important;
    box-shadow:0 5px 16px rgba(20,70,43,.04)!important;
}
.gsm-track-search-v9__input:focus{
    border-color:#72ad88!important;
    box-shadow:0 0 0 3px rgba(24,139,75,.08)!important;
}
.gsm-track-search-v9__btn{
    flex:0 0 auto!important;
    height:46px!important;
    border:0!important;
    border-radius:999px!important;
    padding:0 18px!important;
    background:#f4a024!important;
    color:#fff!important;
    font-size:11px!important;
    font-weight:900!important;
    cursor:pointer!important;
    white-space:nowrap!important;
    box-shadow:0 8px 22px rgba(244,160,36,.18)!important;
}
.gsm-track-search-v9__btn:hover{
    background:#dc8b18!important;
}
@media(max-width:980px){
    .gsm-track-search-v9__inner{
        grid-template-columns:auto 1fr!important;
    }
    .gsm-track-search-v9__form{
        grid-column:1/-1!important;
    }
}
@media(max-width:620px){
    .gsm-track-search-v9{
        padding:14px 0!important;
    }
    .gsm-track-search-v9__inner{
        grid-template-columns:auto 1fr!important;
        gap:12px!important;
        padding:15px!important;
    }
    .gsm-track-search-v9__form{
        flex-direction:column!important;
        align-items:stretch!important;
    }
    .gsm-track-search-v9__btn{
        width:100%!important;
    }
}
CSS);
?>

<span class="gsm-home-v6-marker" data-home-version="V9-PUBLISH-READY">V6</span>
<section class="gsm-hero-slider" data-hero-slider data-autoplay="6500">
    <div class="gsm-hero-track">
        <?php if ($heroSlides): ?>
            <?php foreach ($heroSlides as $index => $slide): ?>
                <?php
                $desktop = $publicUrl($slide->desktop_image);
                $mobile = $publicUrl($slide->mobile_image ?: $slide->desktop_image);

                $hasBadge = trim((string)$slide->badge) !== '';
                $hasTitle = trim((string)$slide->title) !== '';
                $hasSubtitle = trim((string)$slide->subtitle) !== '';

                $hasPrimaryButton =
                    trim((string)$slide->primary_label) !== ''
                    && trim((string)$slide->primary_url) !== '';

                $hasSecondaryButton =
                    trim((string)$slide->secondary_label) !== ''
                    && trim((string)$slide->secondary_url) !== '';

                $hasContent =
                    $hasBadge
                    || $hasTitle
                    || $hasSubtitle
                    || $hasPrimaryButton
                    || $hasSecondaryButton;
                ?>

                <article
                    class="gsm-hero-slide <?= $index === 0 ? 'is-active' : '' ?> <?= $hasContent ? 'has-content' : '' ?>"
                    data-slide-index="<?= (int)$index ?>"
                >
                    <picture class="gsm-hero-media">
                        <source
                            media="(max-width: 720px)"
                            srcset="<?= Html::encode($mobile) ?>"
                        >
                        <img
                            src="<?= Html::encode($desktop) ?>"
                            alt="<?= Html::encode($hasTitle ? $slide->title : 'Banner Gerakan Sumut Mengajar') ?>"
                            loading="<?= $index === 0 ? 'eager' : 'lazy' ?>"
                            fetchpriority="<?= $index === 0 ? 'high' : 'auto' ?>"
                        >
                    </picture>

                    <?php if ($hasContent): ?>
                        <div class="gsm-hero-overlay"></div>

                        <div class="container gsm-hero-content">
                            <?php if ($hasBadge): ?>
                                <span class="gsm-hero-badge">
                                    <?= Html::encode($slide->badge) ?>
                                </span>
                            <?php endif; ?>

                            <?php if ($hasTitle): ?>
                                <h1><?= Html::encode($slide->title) ?></h1>
                            <?php endif; ?>

                            <?php if ($hasSubtitle): ?>
                                <p><?= Html::encode($slide->subtitle) ?></p>
                            <?php endif; ?>

                            <?php if ($hasPrimaryButton || $hasSecondaryButton): ?>
                                <div class="gsm-hero-buttons">
                                    <?php if ($hasPrimaryButton): ?>
                                        <a
                                            class="gsm-hero-primary"
                                            href="<?= Html::encode($slide->primary_url) ?>"
                                        >
                                            <?= Html::encode($slide->primary_label) ?> →
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($hasSecondaryButton): ?>
                                        <a
                                            class="gsm-hero-secondary"
                                            href="<?= Html::encode($slide->secondary_url) ?>"
                                        >
                                            <?= Html::encode($slide->secondary_label) ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <!--
                Fallback juga gambar-only.
                Tidak ada teks/tombol otomatis yang dipaksakan.
            -->
            <article class="gsm-hero-slide is-active">
                <picture class="gsm-hero-media">
                    <img
                        src="<?= $imgBase ?>/hero-batch20.jpg"
                        alt="Gerakan Sumut Mengajar"
                        loading="eager"
                        fetchpriority="high"
                    >
                </picture>
            </article>
        <?php endif; ?>
    </div>

    <?php if (count($heroSlides) > 1): ?>
        <button
            class="gsm-hero-arrow prev"
            type="button"
            aria-label="Banner sebelumnya"
        >‹</button>

        <button
            class="gsm-hero-arrow next"
            type="button"
            aria-label="Banner berikutnya"
        >›</button>

        <div class="gsm-hero-dots">
            <?php foreach ($heroSlides as $i => $slide): ?>
                <button
                    type="button"
                    class="<?= $i === 0 ? 'is-active' : '' ?>"
                    data-slide-dot="<?= (int)$i ?>"
                    aria-label="Banner <?= (int)$i + 1 ?>"
                ></button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>


<section class="gsm-track-search-v9">
    <div class="container">
        <div class="gsm-track-search-v9__inner">
            <div class="gsm-track-search-v9__icon" aria-hidden="true">✓</div>

            <div class="gsm-track-search-v9__copy">
                <small>SUDAH MENDAFTAR?</small>
                <h3>Cek status pendaftaran.</h3>
                <p>Kode pendaftaran tersedia di Portal Peserta.</p>
            </div>

            <form
                class="gsm-track-search-v9__form"
                action="<?= Url::to(['/site/track']) ?>"
                method="get"
            >
                <input
                    class="gsm-track-search-v9__input"
                    type="text"
                    name="kode"
                    maxlength="40"
                    autocomplete="off"
                    aria-label="Kode pendaftaran"
                    placeholder="Contoh: GSM-2026-01-00006"
                    required
                >

                <button class="gsm-track-search-v9__btn" type="submit">
                    Cek Status →
                </button>
            </form>
        </div>
    </div>
</section>

<section id="tentang" class="section">
    <div class="container gsm-pair-v6 gsm-pair-v6--about">
        <div class="gsm-pair-v6__media">
            <img
                src="<?= $imgBase ?>/home-about-activity.jpg"
                alt="Aktivitas Sumut Mengajar"
            >
        </div>

        <div class="gsm-pair-v6__copy">
            <span class="section-kicker">TENTANG KAMI</span>
            <h2>Gerakan yang mempertemukan kepedulian dengan aksi.</h2>
            <p>
                Sumut Mengajar menjadi ruang bagi relawan muda untuk terlibat
                langsung dalam pendidikan dan pengabdian masyarakat di berbagai
                daerah Sumatera Utara.
            </p>
            <a href="<?= Url::to(['/site/about']) ?>" class="text-link">
                Cerita dan makna identitas kami →
            </a>
        </div>
    </div>
</section>

<section class="impact">
    <div class="container impact-grid">
        <div>
            <b>33</b>
            <span>Kabupaten/Kota Sumatera Utara</span>
        </div>
        <div>
            <b>20+</b>
            <span>Batch perjalanan pengabdian</span>
        </div>
        <div>
            <b><?= (int)$alumniStats['total'] ?></b>
            <span>Profil alumni yang sudah dipublikasikan</span>
        </div>
    </div>
</section>

<section id="program" class="section soft">
    <div class="container">
        <span class="section-kicker">PROGRAM</span>

        <div class="section-head">
            <h2>Belajar, mengajar, dan tumbuh bersama.</h2>
            <p>
                Setiap pengabdian mempertemukan relawan, sekolah, masyarakat,
                dan mitra dalam pengalaman belajar yang nyata.
            </p>
        </div>

        <div class="program-grid">
            <article>
                <span>01</span>
                <h3>Volunteer Mengajar</h3>
                <p>
                    Kegiatan pengabdian pendidikan bersama sekolah dan
                    masyarakat setempat.
                </p>
            </article>

            <article>
                <span>02</span>
                <h3>Literasi & Inspirasi</h3>
                <p>
                    Aktivitas belajar yang mendorong rasa ingin tahu,
                    kreativitas, dan semangat anak.
                </p>
            </article>

            <article>
                <span>03</span>
                <h3>Kolaborasi Daerah</h3>
                <p>
                    Menghubungkan relawan, komunitas, sekolah, dan mitra dalam
                    satu gerakan.
                </p>
            </article>
        </div>
    </div>
</section>

<section id="pengabdian" class="section">
    <div class="container">
        <span class="section-kicker">JEJAK PENGABDIAN</span>

        <div class="section-head">
            <h2>Jelajahi Sumatera Utara bersama Sumut Mengajar.</h2>
            <p>
                Jejak pengabdian tumbuh dari batch ke batch, membawa cerita dari
                berbagai daerah.
            </p>
        </div>

        <div class="region-preview">
            <span>SAMOSIR</span>
            <span>SIBOLANGIT</span>
            <span>KUTALIMBARU</span>
            <span>KARO</span>
            <span>DAIRI</span>
            <span>LANGKAT</span>
        </div>
    </div>
</section>

<section class="recruitment-feature">
    <div class="container gsm-pair-v6 gsm-pair-v6--recruit">
        <div class="gsm-pair-v6__copy">
            <?php if ($activeBatch): ?>
                <span class="status-open">PENDAFTARAN DIBUKA</span>

                <h2><?= Html::encode($activeBatch->title) ?></h2>

                <p>
                    <?= Html::encode(
                        $activeBatch->description
                        ?: 'Kesempatan untuk belajar, mengabdi, dan bertumbuh bersama Sumut Mengajar.'
                    ) ?>
                </p>

                <div class="batch-meta">
                    <span>Batch <?= (int)$activeBatch->batch_number ?></span>
                    <span>
                        <?= Html::encode($activeBatch->getLocationLabel()) ?>
                    </span>
                    <span>
                        s.d.
                        <?= Yii::$app->formatter->asDate($activeBatch->registration_end) ?>
                    </span>
                </div>

                <a
                    class="btn-light"
                    href="<?= Url::to(['/recruitment/view', 'slug' => $activeBatch->slug]) ?>"
                >
                    Lihat Rekrutmen →
                </a>
            <?php else: ?>
                <span class="status-soon">REKRUTMEN</span>
                <h2>Batch berikutnya segera hadir.</h2>
                <p>
                    Pantau informasi rekrutmen terbaru dari Gerakan Sumut
                    Mengajar.
                </p>
                <a
                    class="btn-light"
                    href="<?= Url::to(['/recruitment/index']) ?>"
                >
                    Lihat Rekrutmen →
                </a>
            <?php endif; ?>
        </div>

        <div class="gsm-pair-v6__media">
            <img
                src="<?= $imgBase ?>/recruitment-samosir-reference.png"
                alt="Rekrutmen Sumut Mengajar"
            >
        </div>
    </div>
</section>

<section class="section alumni-home-section">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="section-kicker">JEJARING ALUMNI</span>
                <h2>Pengabdian selesai, dampaknya terus berjalan.</h2>
            </div>

            <div>
                <p>
                    Temukan alumni dari berbagai batch, perjalanan karier, serta
                    karya dan riset yang lahir setelah pengabdian.
                </p>
                <a class="text-link" href="<?= Url::to(['/alumni/index']) ?>">
                    Jelajahi Alumni →
                </a>
            </div>
        </div>

        <?php if ($featuredAlumni): ?>
            <div class="alumni-home-grid">
                <?php foreach ($featuredAlumni as $alumni): ?>
                    <?php $photo = $publicUrl($alumni->photo); ?>

                    <a
                        class="alumni-home-card"
                        href="<?= Url::to(['/alumni/view', 'slug' => $alumni->slug]) ?>"
                    >
                        <div class="alumni-home-photo">
                            <?php if ($photo): ?>
                                <img
                                    src="<?= Html::encode($photo) ?>"
                                    alt="<?= Html::encode($alumni->nama_lengkap) ?>"
                                >
                            <?php else: ?>
                                <span>
                                    <?= Html::encode(
                                        mb_strtoupper(mb_substr($alumni->nama_lengkap, 0, 1))
                                    ) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <small>
                            BATCH <?= (int)$alumni->batch_number ?>
                            ·
                            <?= Html::encode(
                                mb_strtoupper($alumni->location_name ?: 'SUMUT')
                            ) ?>
                        </small>

                        <h3><?= Html::encode($alumni->nama_lengkap) ?></h3>

                        <p>
                            <?= Html::encode(
                                $alumni->current_position ?: 'Alumni Sumut Mengajar'
                            ) ?>
                            <?= $alumni->current_institution
                                ? ' · ' . Html::encode($alumni->current_institution)
                                : '' ?>
                        </p>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alumni-home-empty">
                <h3>Database alumni sedang dibangun.</h3>
                <p>
                    Alumni dari batch terdahulu dapat mendaftarkan profil,
                    karier, dan karya untuk diverifikasi oleh tim Sumut Mengajar.
                </p>
                <a
                    class="btn-primary-gsm"
                    href="<?= Url::to(['/alumni/register']) ?>"
                >
                    Daftarkan Alumni →
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section id="cerita" class="section soft">
    <div class="container">
        <span class="section-kicker">CERITA DARI LAPANGAN</span>

        <div class="section-head">
            <h2>Bukan hanya tentang mengajar.</h2>
            <p>
                Tentang pertemuan, pengalaman, dan cerita yang terus hidup
                setelah pengabdian selesai.
            </p>
        </div>

        <div class="story-grid">
            <article>
                <div class="story-photo">
                    <img
                        src="<?= $imgBase ?>/story-donation.jpg"
                        alt="Aksi sosial Sumut Mengajar"
                    >
                </div>
                <small>AKSI SOSIAL</small>
                <h3>
                    Bersama turun ke lapangan dan menyalakan kepedulian.
                </h3>
            </article>

            <article>
                <div class="story-photo">
                    <img
                        src="<?= $imgBase ?>/story-journey.jpg"
                        alt="Perjalanan pengabdian"
                    >
                </div>
                <small>PERJALANAN PENGABDIAN</small>
                <h3>
                    Berangkat sebagai relawan, pulang membawa keluarga dan
                    pengalaman baru.
                </h3>
            </article>

            <article>
                <div class="story-photo">
                    <img
                        src="<?= $imgBase ?>/story-ceremony.jpg"
                        alt="Pembekalan dan pelepasan"
                    >
                </div>
                <small>PEMBEKALAN & PELEPASAN</small>
                <h3>
                    Setiap langkah dimulai dengan semangat, tanggung jawab, dan
                    harapan.
                </h3>
            </article>
        </div>
    </div>
</section>

<section class="cta">
    <div class="container cta-inner">
        <div>
            <span>SIAP BERGERAK?</span>
            <h2>
                Jadilah bagian dari cerita Sumut Mengajar berikutnya.
            </h2>
        </div>

        <a href="<?= Url::to(['/site/register']) ?>">
            Buat Akun Peserta →
        </a>
    </div>
</section>
