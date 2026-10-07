<?php
use app\models\AlumniPublication;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $model->nama_lengkap . ' — Alumni Sumut Mengajar';

$photo = $model->photo
    ? Yii::$app->request->baseUrl . '/' . ltrim($model->photo, '/')
    : null;

$publicCareers = array_values(array_filter(
    $model->careers,
    static fn($career) => (int) $career->is_public === 1
));

$publications = $model->publications;
?>

<div class="gsm-alumni-detail-v26">

    <section class="gsm-alumni-detail-v26__hero">
        <div class="gsm-alumni-detail-v26__wrap">
            <a class="gsm-alumni-detail-v26__back" href="<?= Url::to(['/alumni/index']) ?>">
                <span aria-hidden="true">←</span> Kembali ke Alumni
            </a>

            <div class="gsm-alumni-detail-v26__hero-grid">
                <div class="gsm-alumni-detail-v26__photo">
                    <?php if ($photo): ?>
                        <img
                            src="<?= Html::encode($photo) ?>"
                            alt="<?= Html::encode($model->nama_lengkap) ?>"
                        >
                    <?php else: ?>
                        <span>
                            <?= Html::encode(mb_strtoupper(mb_substr($model->nama_lengkap, 0, 1))) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="gsm-alumni-detail-v26__intro">
                    <span class="gsm-alumni-detail-v26__eyebrow">
                        ALUMNI BATCH <?= (int) $model->batch_number ?>
                    </span>

                    <h1><?= Html::encode($model->nama_lengkap) ?></h1>

                    <p class="gsm-alumni-detail-v26__lead">
                        <?= Html::encode($model->current_position ?: 'Alumni Sumut Mengajar') ?>
                        <?php if ($model->current_institution): ?>
                            <span>di <?= Html::encode($model->current_institution) ?></span>
                        <?php endif; ?>
                    </p>

                    <div class="gsm-alumni-detail-v26__meta">
                        <div>
                            <small>LOKASI PENGABDIAN</small>
                            <strong><?= Html::encode($model->location_name ?: 'Sumatera Utara') ?></strong>
                        </div>

                        <?php if ($model->batch_year): ?>
                            <div>
                                <small>TAHUN</small>
                                <strong><?= (int) $model->batch_year ?></strong>
                            </div>
                        <?php endif; ?>

                        <?php if ($model->sector): ?>
                            <div>
                                <small>SEKTOR</small>
                                <strong><?= Html::encode($model->sector) ?></strong>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="gsm-alumni-detail-v26__content">
        <div class="gsm-alumni-detail-v26__wrap">
            <div class="gsm-alumni-detail-v26__layout">

                <main class="gsm-alumni-detail-v26__main">

                    <article class="gsm-alumni-detail-v26__card gsm-alumni-detail-v26__story">
                        <span class="gsm-alumni-detail-v26__kicker">CERITA ALUMNI</span>
                        <h2>Perjalanan setelah pengabdian.</h2>

                        <div class="gsm-alumni-detail-v26__prose">
                            <?= nl2br(Html::encode(
                                $model->bio ?: 'Profil ini sedang dilengkapi oleh tim Sumut Mengajar.'
                            )) ?>
                        </div>
                    </article>

                    <article class="gsm-alumni-detail-v26__card">
                        <div class="gsm-alumni-detail-v26__section-head">
                            <div>
                                <span class="gsm-alumni-detail-v26__kicker">KARIER</span>
                                <h2>Jejak profesional.</h2>
                            </div>
                            <?php if ($publicCareers): ?>
                                <span class="gsm-alumni-detail-v26__count">
                                    <?= count($publicCareers) ?> riwayat
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if (!$publicCareers): ?>
                            <p class="gsm-alumni-detail-v26__empty">
                                Riwayat karier belum dipublikasikan.
                            </p>
                        <?php else: ?>
                            <div class="gsm-alumni-detail-v26__timeline">
                                <?php foreach ($publicCareers as $career): ?>
                                    <div class="gsm-alumni-detail-v26__career">
                                        <div class="gsm-alumni-detail-v26__career-dot"></div>
                                        <div>
                                            <span class="gsm-alumni-detail-v26__period">
                                                <?= Html::encode($career->start_year ?: '') ?>
                                                <?php if ($career->is_current): ?>
                                                    — Sekarang
                                                <?php elseif ($career->end_year): ?>
                                                    — <?= Html::encode($career->end_year) ?>
                                                <?php endif; ?>
                                            </span>

                                            <h3><?= Html::encode($career->position_title) ?></h3>

                                            <p>
                                                <?= Html::encode($career->institution_name) ?>
                                                <?php if ($career->city): ?>
                                                    <span>· <?= Html::encode($career->city) ?></span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </article>

                    <article class="gsm-alumni-detail-v26__card">
                        <div class="gsm-alumni-detail-v26__section-head">
                            <div>
                                <span class="gsm-alumni-detail-v26__kicker">KARYA & RISET</span>
                                <h2>Karya yang ditinggalkan.</h2>
                            </div>
                            <?php if ($publications): ?>
                                <span class="gsm-alumni-detail-v26__count">
                                    <?= count($publications) ?> karya
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if (!$publications): ?>
                            <p class="gsm-alumni-detail-v26__empty">
                                Belum ada karya yang dipublikasikan.
                            </p>
                        <?php else: ?>
                            <div class="gsm-alumni-detail-v26__publications">
                                <?php foreach ($publications as $publication): ?>
                                    <article class="gsm-alumni-detail-v26__publication">
                                        <div class="gsm-alumni-detail-v26__badges">
                                            <span>
                                                <?= Html::encode(
                                                    AlumniPublication::TYPES[$publication->publication_type]
                                                    ?? $publication->publication_type
                                                ) ?>
                                            </span>

                                            <?php if ($publication->is_about_service): ?>
                                                <span class="is-service">Riset tentang Pengabdian</span>
                                            <?php endif; ?>
                                        </div>

                                        <h3><?= Html::encode($publication->title) ?></h3>

                                        <?php if ($publication->institution_or_publisher || $publication->publication_year): ?>
                                            <p class="gsm-alumni-detail-v26__publication-meta">
                                                <?= Html::encode($publication->institution_or_publisher ?: '') ?>
                                                <?php if ($publication->institution_or_publisher && $publication->publication_year): ?>
                                                    ·
                                                <?php endif; ?>
                                                <?= $publication->publication_year ? (int) $publication->publication_year : '' ?>
                                            </p>
                                        <?php endif; ?>

                                        <?php if ($publication->summary): ?>
                                            <div class="gsm-alumni-detail-v26__publication-summary">
                                                <?= nl2br(Html::encode($publication->summary)) ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($publication->url): ?>
                                            <a
                                                class="gsm-alumni-detail-v26__publication-link"
                                                href="<?= Html::encode($publication->url) ?>"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                            >
                                                Baca / lihat karya <span aria-hidden="true">→</span>
                                            </a>
                                        <?php endif; ?>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </article>

                </main>

                <aside class="gsm-alumni-detail-v26__aside">
                    <div class="gsm-alumni-detail-v26__side-card">
                        <span class="gsm-alumni-detail-v26__side-kicker">RINGKASAN</span>
                        <h3>Profil Singkat</h3>

                        <dl>
                            <div>
                                <dt>Batch</dt>
                                <dd><?= (int) $model->batch_number ?></dd>
                            </div>
                            <div>
                                <dt>Lokasi Pengabdian</dt>
                                <dd><?= Html::encode($model->location_name ?: '-') ?></dd>
                            </div>
                            <div>
                                <dt>Jabatan Saat Ini</dt>
                                <dd><?= Html::encode($model->current_position ?: '-') ?></dd>
                            </div>
                            <div>
                                <dt>Instansi</dt>
                                <dd><?= Html::encode($model->current_institution ?: '-') ?></dd>
                            </div>
                            <div>
                                <dt>Kota Bekerja</dt>
                                <dd><?= Html::encode($model->work_city ?: '-') ?></dd>
                            </div>
                        </dl>
                    </div>

                    <?php if ($model->linkedin || $model->instagram): ?>
                        <div class="gsm-alumni-detail-v26__side-card">
                            <span class="gsm-alumni-detail-v26__side-kicker">JEJARING</span>
                            <h3>Terhubung</h3>

                            <div class="gsm-alumni-detail-v26__socials">
                                <?php if ($model->linkedin): ?>
                                    <a
                                        href="<?= Html::encode($model->linkedin) ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        LinkedIn <span aria-hidden="true">↗</span>
                                    </a>
                                <?php endif; ?>

                                <?php if ($model->instagram): ?>
                                    <div>
                                        <small>Instagram</small>
                                        <strong><?= Html::encode($model->instagram) ?></strong>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </aside>

            </div>
        </div>
    </section>

</div>

<?php
$this->registerCss(<<<'CSS'
/* =========================================================
   Alumni Detail V26
   Isolated classes prevent legacy Bootstrap/container/grid
   styles from collapsing the detail page.
   ========================================================= */

.gsm-alumni-detail-v26{
    --gsm-green:#0e623a;
    --gsm-green-2:#16824a;
    --gsm-deep:#1d2d23;
    --gsm-text:#4f5f55;
    --gsm-muted:#7a877f;
    --gsm-line:#e0e8e2;
    --gsm-soft:#f6f9f7;
    --gsm-cream:#fbf7ef;
    --gsm-orange:#e9a23b;
    color:var(--gsm-text);
    background:#fff;
}

.gsm-alumni-detail-v26 *,
.gsm-alumni-detail-v26 *::before,
.gsm-alumni-detail-v26 *::after{
    box-sizing:border-box;
}

.gsm-alumni-detail-v26__wrap{
    width:min(1160px,calc(100% - 48px));
    margin:0 auto;
}

.gsm-alumni-detail-v26__wrap::before,
.gsm-alumni-detail-v26__wrap::after,
.gsm-alumni-detail-v26__layout::before,
.gsm-alumni-detail-v26__layout::after,
.gsm-alumni-detail-v26__hero-grid::before,
.gsm-alumni-detail-v26__hero-grid::after{
    display:none!important;
    content:none!important;
}

/* Hero */
.gsm-alumni-detail-v26__hero{
    position:relative;
    overflow:hidden;
    padding:34px 0 46px;
    border-bottom:1px solid #e8eee9;
    background:
        radial-gradient(circle at 88% 10%,rgba(233,162,59,.15),transparent 26%),
        radial-gradient(circle at 12% 86%,rgba(22,130,74,.09),transparent 30%),
        linear-gradient(180deg,#fbfdfb 0%,#f6faf7 100%);
}

.gsm-alumni-detail-v26__back{
    display:inline-flex;
    align-items:center;
    gap:7px;
    margin-bottom:24px;
    color:#617168!important;
    font-size:11px;
    font-weight:800;
    text-decoration:none!important;
    transition:color .18s ease;
}

.gsm-alumni-detail-v26__back:hover{
    color:var(--gsm-green)!important;
}

.gsm-alumni-detail-v26__hero-grid{
    display:grid!important;
    grid-template-columns:220px minmax(0,1fr)!important;
    gap:42px!important;
    align-items:center!important;
    width:100%!important;
    margin:0!important;
}

.gsm-alumni-detail-v26__photo{
    display:grid;
    place-items:center;
    width:220px;
    aspect-ratio:3/4;
    overflow:hidden;
    border:7px solid #fff;
    border-radius:24px;
    background:linear-gradient(145deg,#e7f3eb,#f6e8cf);
    box-shadow:0 20px 55px rgba(30,66,45,.14);
}

.gsm-alumni-detail-v26__photo img{
    display:block;
    width:100%;
    height:100%;
    object-fit:cover;
}

.gsm-alumni-detail-v26__photo>span{
    color:var(--gsm-green);
    font-size:78px;
    font-weight:900;
}

.gsm-alumni-detail-v26__intro{
    min-width:0;
}

.gsm-alumni-detail-v26__eyebrow,
.gsm-alumni-detail-v26__kicker,
.gsm-alumni-detail-v26__side-kicker{
    display:block;
    color:var(--gsm-green-2);
    font-size:9px;
    font-weight:900;
    line-height:1.3;
    letter-spacing:1px;
    text-transform:uppercase;
}

.gsm-alumni-detail-v26__intro h1{
    max-width:800px;
    margin:8px 0 10px;
    color:var(--gsm-deep);
    font-size:clamp(34px,4.3vw,58px);
    font-weight:900;
    line-height:1.05;
    letter-spacing:-2px;
}

.gsm-alumni-detail-v26__lead{
    max-width:760px;
    margin:0;
    color:#617168;
    font-size:16px;
    line-height:1.55;
}

.gsm-alumni-detail-v26__lead span{
    color:#394c40;
    font-weight:750;
}

.gsm-alumni-detail-v26__meta{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    margin-top:26px;
}

.gsm-alumni-detail-v26__meta>div{
    min-width:150px;
    padding:11px 14px;
    border:1px solid rgba(14,98,58,.11);
    border-radius:13px;
    background:rgba(255,255,255,.78);
    backdrop-filter:blur(8px);
}

.gsm-alumni-detail-v26__meta small{
    display:block;
    margin-bottom:4px;
    color:#8a968f;
    font-size:8px;
    font-weight:850;
    letter-spacing:.65px;
}

.gsm-alumni-detail-v26__meta strong{
    display:block;
    color:#314339;
    font-size:11px;
    font-weight:850;
    line-height:1.4;
}

/* Content */
.gsm-alumni-detail-v26__content{
    padding:54px 0 74px;
    background:#fff;
}

.gsm-alumni-detail-v26__layout{
    display:grid!important;
    grid-template-columns:minmax(0,1fr) 300px!important;
    gap:28px!important;
    align-items:start!important;
    width:100%!important;
    margin:0!important;
}

.gsm-alumni-detail-v26__main{
    display:grid;
    gap:20px;
    min-width:0;
}

.gsm-alumni-detail-v26__card,
.gsm-alumni-detail-v26__side-card{
    border:1px solid var(--gsm-line);
    background:#fff;
    box-shadow:0 12px 38px rgba(25,64,42,.045);
}

.gsm-alumni-detail-v26__card{
    padding:30px;
    border-radius:22px;
}

.gsm-alumni-detail-v26__story{
    background:
        linear-gradient(135deg,rgba(14,98,58,.025),transparent 44%),
        #fff;
}

.gsm-alumni-detail-v26__card h2{
    margin:6px 0 17px;
    color:var(--gsm-deep);
    font-size:26px;
    font-weight:900;
    line-height:1.18;
    letter-spacing:-.7px;
}

.gsm-alumni-detail-v26__prose{
    color:#58675e;
    font-size:13px;
    line-height:1.9;
}

.gsm-alumni-detail-v26__section-head{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:20px;
    margin-bottom:4px;
}

.gsm-alumni-detail-v26__section-head h2{
    margin-bottom:0;
}

.gsm-alumni-detail-v26__count{
    flex:0 0 auto;
    margin-top:4px;
    padding:6px 9px;
    border-radius:999px;
    background:#eef7f1;
    color:var(--gsm-green);
    font-size:9px;
    font-weight:850;
}

.gsm-alumni-detail-v26__empty{
    margin:18px 0 0;
    padding:15px 16px;
    border-radius:12px;
    background:var(--gsm-soft);
    color:#7c8981;
    font-size:11px;
}

/* Career */
.gsm-alumni-detail-v26__timeline{
    position:relative;
    display:grid;
    gap:0;
    margin-top:22px;
}

.gsm-alumni-detail-v26__timeline::before{
    content:"";
    position:absolute;
    top:8px;
    bottom:8px;
    left:6px;
    width:1px;
    background:#dfe9e2;
}

.gsm-alumni-detail-v26__career{
    position:relative;
    display:grid;
    grid-template-columns:26px minmax(0,1fr);
    gap:10px;
    padding:0 0 24px;
}

.gsm-alumni-detail-v26__career:last-child{
    padding-bottom:0;
}

.gsm-alumni-detail-v26__career-dot{
    position:relative;
    z-index:1;
    width:13px;
    height:13px;
    margin-top:3px;
    border:3px solid #e8f5ec;
    border-radius:50%;
    background:var(--gsm-green-2);
}

.gsm-alumni-detail-v26__period{
    display:block;
    margin-bottom:5px;
    color:var(--gsm-green-2);
    font-size:9px;
    font-weight:900;
    letter-spacing:.35px;
}

.gsm-alumni-detail-v26__career h3{
    margin:0 0 4px;
    color:#2b3d32;
    font-size:16px;
    font-weight:850;
    line-height:1.35;
}

.gsm-alumni-detail-v26__career p{
    margin:0;
    color:#7a877f;
    font-size:11px;
    line-height:1.55;
}

/* Publications */
.gsm-alumni-detail-v26__publications{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:12px;
    margin-top:22px;
}

.gsm-alumni-detail-v26__publication{
    min-width:0;
    padding:18px;
    border:1px solid #e4ebe6;
    border-radius:16px;
    background:#fbfdfb;
}

.gsm-alumni-detail-v26__badges{
    display:flex;
    flex-wrap:wrap;
    gap:5px;
    margin-bottom:10px;
}

.gsm-alumni-detail-v26__badges span{
    padding:4px 7px;
    border-radius:999px;
    background:#eaf5ee;
    color:var(--gsm-green);
    font-size:8px;
    font-weight:900;
}

.gsm-alumni-detail-v26__badges .is-service{
    background:#fff3df;
    color:#986114;
}

.gsm-alumni-detail-v26__publication h3{
    margin:0;
    color:#2b3d32;
    font-size:14px;
    font-weight:850;
    line-height:1.45;
}

.gsm-alumni-detail-v26__publication-meta{
    margin:7px 0 0;
    color:#89958e;
    font-size:9px;
    line-height:1.5;
}

.gsm-alumni-detail-v26__publication-summary{
    margin-top:10px;
    color:#64736a;
    font-size:10px;
    line-height:1.65;
}

.gsm-alumni-detail-v26__publication-link{
    display:inline-flex;
    align-items:center;
    gap:5px;
    margin-top:12px;
    color:var(--gsm-green)!important;
    font-size:10px;
    font-weight:900;
    text-decoration:none!important;
}

/* Aside */
.gsm-alumni-detail-v26__aside{
    position:sticky;
    top:94px;
    display:grid;
    gap:14px;
    min-width:0;
}

.gsm-alumni-detail-v26__side-card{
    overflow:hidden;
    padding:22px;
    border-radius:18px;
}

.gsm-alumni-detail-v26__side-card h3{
    margin:5px 0 16px;
    color:var(--gsm-deep);
    font-size:19px;
    font-weight:900;
    letter-spacing:-.35px;
}

.gsm-alumni-detail-v26__side-card dl{
    margin:0;
}

.gsm-alumni-detail-v26__side-card dl>div{
    display:grid;
    gap:3px;
    padding:11px 0;
    border-top:1px solid #edf1ee;
}

.gsm-alumni-detail-v26__side-card dl>div:first-child{
    padding-top:0;
    border-top:0;
}

.gsm-alumni-detail-v26__side-card dl>div:last-child{
    padding-bottom:0;
}

.gsm-alumni-detail-v26__side-card dt{
    color:#8b968f;
    font-size:8px;
    font-weight:850;
    letter-spacing:.45px;
    text-transform:uppercase;
}

.gsm-alumni-detail-v26__side-card dd{
    margin:0;
    color:#34463b;
    font-size:11px;
    font-weight:750;
    line-height:1.5;
}

.gsm-alumni-detail-v26__socials{
    display:grid;
    gap:10px;
}

.gsm-alumni-detail-v26__socials>a,
.gsm-alumni-detail-v26__socials>div{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    padding:11px 12px;
    border-radius:11px;
    background:var(--gsm-soft);
    color:#3c5044;
    font-size:10px;
    font-weight:850;
    text-decoration:none!important;
}

.gsm-alumni-detail-v26__socials>a{
    color:var(--gsm-green)!important;
}

.gsm-alumni-detail-v26__socials>div{
    display:block;
}

.gsm-alumni-detail-v26__socials small{
    display:block;
    color:#8a968f;
    font-size:8px;
    font-weight:800;
}

.gsm-alumni-detail-v26__socials strong{
    display:block;
    margin-top:2px;
    overflow-wrap:anywhere;
    color:#3c5044;
    font-size:10px;
}

/* Responsive */
@media(max-width:920px){
    .gsm-alumni-detail-v26__hero-grid{
        grid-template-columns:180px minmax(0,1fr)!important;
        gap:28px!important;
    }

    .gsm-alumni-detail-v26__photo{
        width:180px;
    }

    .gsm-alumni-detail-v26__layout{
        grid-template-columns:minmax(0,1fr) 260px!important;
        gap:20px!important;
    }

    .gsm-alumni-detail-v26__publications{
        grid-template-columns:1fr;
    }
}

@media(max-width:760px){
    .gsm-alumni-detail-v26__wrap{
        width:min(100% - 30px,1160px);
    }

    .gsm-alumni-detail-v26__hero{
        padding:24px 0 34px;
    }

    .gsm-alumni-detail-v26__back{
        margin-bottom:18px;
    }

    .gsm-alumni-detail-v26__hero-grid{
        grid-template-columns:112px minmax(0,1fr)!important;
        gap:18px!important;
        align-items:start!important;
    }

    .gsm-alumni-detail-v26__photo{
        width:112px;
        border-width:4px;
        border-radius:17px;
    }

    .gsm-alumni-detail-v26__photo>span{
        font-size:46px;
    }

    .gsm-alumni-detail-v26__intro h1{
        margin-top:6px;
        font-size:30px;
        letter-spacing:-1px;
    }

    .gsm-alumni-detail-v26__lead{
        font-size:12px;
    }

    .gsm-alumni-detail-v26__meta{
        grid-column:1/-1;
        gap:7px;
        margin-top:17px;
    }

    .gsm-alumni-detail-v26__meta>div{
        min-width:0;
        flex:1 1 120px;
        padding:9px 10px;
    }

    .gsm-alumni-detail-v26__content{
        padding:30px 0 54px;
    }

    .gsm-alumni-detail-v26__layout{
        grid-template-columns:1fr!important;
    }

    .gsm-alumni-detail-v26__aside{
        position:static;
        grid-row:1;
        grid-template-columns:1fr;
    }

    .gsm-alumni-detail-v26__main{
        grid-row:2;
    }

    .gsm-alumni-detail-v26__card{
        padding:20px;
        border-radius:17px;
    }

    .gsm-alumni-detail-v26__card h2{
        font-size:22px;
    }

    .gsm-alumni-detail-v26__prose{
        font-size:12px;
        line-height:1.8;
    }
}

@media(max-width:480px){
    .gsm-alumni-detail-v26__hero-grid{
        grid-template-columns:88px minmax(0,1fr)!important;
        gap:14px!important;
    }

    .gsm-alumni-detail-v26__photo{
        width:88px;
        border-radius:14px;
    }

    .gsm-alumni-detail-v26__intro h1{
        font-size:24px;
    }

    .gsm-alumni-detail-v26__eyebrow{
        font-size:8px;
    }

    .gsm-alumni-detail-v26__meta{
        display:grid;
        grid-template-columns:1fr 1fr;
    }

    .gsm-alumni-detail-v26__meta>div:last-child:nth-child(odd){
        grid-column:1/-1;
    }

    .gsm-alumni-detail-v26__section-head{
        display:block;
    }

    .gsm-alumni-detail-v26__count{
        display:inline-block;
        margin-top:9px;
    }
}
CSS);
?>
