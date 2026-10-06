<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Alumni Sumut Mengajar';

$photoUrl = static fn($path) => $path
    ? Yii::$app->request->baseUrl . '/' . ltrim($path, '/')
    : null;

$alumniCount = (int) ($stats['alumni'] ?? 0);
$locationCount = (int) ($stats['locations'] ?? 0);
$batchCount = (int) ($stats['batches'] ?? 0);
$sectorCount = (int) ($stats['sectors'] ?? 0);
?>

<section class="page-hero gsm-alumni-hero-v224">
    <div class="container">
        <span class="section-kicker">JEJARING ALUMNI</span>
        <h1>Jejak pengabdian yang terus tumbuh.</h1>
        <p>
            Kenali alumni Sumut Mengajar, perjalanan karier mereka,
            serta karya dan riset yang lahir dari pengalaman pengabdian.
        </p>
        <div class="gsm-alumni-hero-v224__actions">
            <a class="btn-primary-gsm" href="<?= Url::to(['/alumni/register']) ?>">
                Daftarkan Profil Alumni →
            </a>
        </div>
    </div>
</section>

<section class="gsm-alumni-stats-v225" aria-label="Statistik alumni">
    <div class="gsm-alumni-stats-v225__grid">
        <article class="gsm-alumni-stat-v225">
            <span class="gsm-alumni-stat-v225__label">Alumni Terverifikasi</span>
            <b><?= $alumniCount ?></b>
            <small>profil alumni yang telah dipublikasikan</small>
        </article>

        <article class="gsm-alumni-stat-v225">
            <span class="gsm-alumni-stat-v225__label">Kabupaten/Kota</span>
            <b><?= $locationCount ?></b>
            <small>lokasi pengabdian yang terwakili</small>
        </article>

        <article class="gsm-alumni-stat-v225">
            <span class="gsm-alumni-stat-v225__label">Batch Terwakili</span>
            <b><?= $batchCount ?></b>
            <small>angkatan pengabdian dalam jejaring alumni</small>
        </article>

        <article class="gsm-alumni-stat-v225">
            <span class="gsm-alumni-stat-v225__label">Sektor Profesi</span>
            <b><?= $sectorCount ?></b>
            <small>ragam bidang kerja alumni</small>
        </article>
    </div>
</section>

<section class="section soft gsm-alumni-directory-v224">
    <div class="container">
        <div class="gsm-alumni-directory-v224__head">
            <div>
                <span class="section-kicker">DIREKTORI ALUMNI</span>
                <h2>Cerita, karya, dan dampak alumni.</h2>
            </div>
            <p>
                Temukan alumni berdasarkan nama, instansi, jabatan,
                lokasi pengabdian, batch, atau sektor profesi.
            </p>
        </div>

        <div class="gsm-alumni-filter-v224">
            <?= Html::beginForm(['/alumni/index'], 'get') ?>
            <div class="gsm-alumni-filter-v224__grid">
                <div class="gsm-alumni-filter-v224__search">
                    <label>Cari alumni</label>
                    <?= Html::textInput('q', $q, [
                        'placeholder' => 'Nama, instansi, jabatan, atau lokasi',
                        'autocomplete' => 'off',
                    ]) ?>
                </div>

                <div>
                    <label>Batch</label>
                    <?php $batchOptions = $batches ? array_combine($batches, $batches) : []; ?>
                    <?= Html::dropDownList('batch', $batch, $batchOptions, ['prompt' => 'Semua Batch']) ?>
                </div>

                <div>
                    <label>Sektor</label>
                    <?php $sectorOptions = $sectors ? array_combine($sectors, $sectors) : []; ?>
                    <?= Html::dropDownList('sector', $sector, $sectorOptions, ['prompt' => 'Semua Sektor']) ?>
                </div>

                <div class="gsm-alumni-filter-v224__actions">
                    <button type="submit">Cari</button>
                    <?php if ($q !== '' || $batch > 0 || $sector !== ''): ?>
                        <a href="<?= Url::to(['/alumni/index']) ?>">Reset</a>
                    <?php endif; ?>
                </div>
            </div>
            <?= Html::endForm() ?>
        </div>

        <?php if (!$models): ?>
            <div class="gsm-alumni-empty-v224">
                <div class="gsm-alumni-empty-v224__mark">SM</div>
                <h3>Belum ada profil alumni yang ditampilkan.</h3>
                <p>
                    Profil akan muncul setelah data alumni diverifikasi,
                    mendapatkan persetujuan publikasi, dan diterbitkan oleh admin.
                </p>
                <a class="btn-primary-gsm" href="<?= Url::to(['/alumni/register']) ?>">
                    Daftarkan Profil Alumni →
                </a>
            </div>
        <?php else: ?>
            <div class="gsm-alumni-grid-v224">
                <?php foreach ($models as $m): ?>
                    <?php $url = Url::to(['/alumni/view', 'slug' => $m->slug]); ?>
                    <article class="gsm-alumni-card-v224">
                        <a class="gsm-alumni-card-v224__photo" href="<?= $url ?>">
                            <?php if ($photoUrl($m->photo)): ?>
                                <img
                                    src="<?= Html::encode($photoUrl($m->photo)) ?>"
                                    alt="<?= Html::encode($m->nama_lengkap) ?>"
                                    loading="lazy"
                                >
                            <?php else: ?>
                                <span><?= Html::encode(mb_strtoupper(mb_substr($m->nama_lengkap, 0, 1))) ?></span>
                            <?php endif; ?>
                        </a>

                        <div class="gsm-alumni-card-v224__body">
                            <small>
                                ALUMNI BATCH <?= (int) $m->batch_number ?>
                                ·
                                <?= Html::encode(mb_strtoupper($m->location_name ?: 'SUMATERA UTARA')) ?>
                            </small>

                            <h3><a href="<?= $url ?>"><?= Html::encode($m->nama_lengkap) ?></a></h3>

                            <p>
                                <?= Html::encode($m->current_position ?: 'Alumni Sumut Mengajar') ?>
                                <?= $m->current_institution ? ' · ' . Html::encode($m->current_institution) : '' ?>
                            </p>

                            <div class="gsm-alumni-card-v224__meta">
                                <?php if ($m->sector): ?>
                                    <span><?= Html::encode($m->sector) ?></span>
                                <?php endif; ?>
                                <?php if ($m->publications): ?>
                                    <span><?= count($m->publications) ?> karya</span>
                                <?php endif; ?>
                            </div>

                            <a class="gsm-alumni-card-v224__link" href="<?= $url ?>">Lihat perjalanan →</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if ($pagination->pageCount > 1): ?>
                <div class="gsm-alumni-pagination-v224">
                    <?= LinkPager::widget(['pagination' => $pagination]) ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php
$this->registerCss(<<<'CSS'
.gsm-alumni-hero-v224 .container{
    max-width:1040px;
}
.gsm-alumni-hero-v224 p{
    max-width:780px;
}
.gsm-alumni-hero-v224__actions{
    margin-top:24px;
}

/* Isolated stats so legacy .alumni-stats styles cannot break the layout. */
.gsm-alumni-stats-v225{
    padding:28px 0;
    background:#174a76;
    color:#fff;
}
.gsm-alumni-stats-v225__grid{
    display:grid!important;
    grid-template-columns:repeat(4,minmax(0,1fr))!important;
    grid-auto-flow:row!important;
    gap:0!important;
    width:min(1180px,calc(100% - 48px))!important;
    margin:0 auto!important;
}
.gsm-alumni-stats-v225__grid::before,
.gsm-alumni-stats-v225__grid::after{
    display:none!important;
    content:none!important;
}
.gsm-alumni-stat-v225{
    display:flex!important;
    flex-direction:column!important;
    justify-content:center!important;
    min-width:0!important;
    min-height:118px!important;
    padding:18px 28px!important;
    margin:0!important;
    border-right:1px solid rgba(255,255,255,.16)!important;
    border-bottom:0!important;
    background:transparent!important;
    grid-column:auto!important;
    grid-row:auto!important;
}
.gsm-alumni-stat-v225:last-child{
    border-right:0!important;
}
.gsm-alumni-stat-v225__label{
    display:block!important;
    margin:0 0 8px!important;
    color:rgba(255,255,255,.68)!important;
    font-size:9px!important;
    font-weight:850!important;
    letter-spacing:.85px!important;
    line-height:1.35!important;
    text-transform:uppercase!important;
}
.gsm-alumni-stat-v225 b{
    display:block!important;
    margin:0!important;
    color:#ffd15c!important;
    font-size:37px!important;
    font-weight:900!important;
    line-height:1!important;
    letter-spacing:-1px!important;
}
.gsm-alumni-stat-v225 small{
    display:block!important;
    margin-top:7px!important;
    color:rgba(255,255,255,.76)!important;
    font-size:10px!important;
    line-height:1.45!important;
}

.gsm-alumni-directory-v224{
    padding-top:58px;
}
.gsm-alumni-directory-v224__head{
    display:flex;
    align-items:end;
    justify-content:space-between;
    gap:28px;
    margin-bottom:22px;
}
.gsm-alumni-directory-v224__head h2{
    margin:5px 0 0;
    color:#1d2d23;
    font-size:32px;
    letter-spacing:-1px;
}
.gsm-alumni-directory-v224__head>p{
    max-width:440px;
    margin:0;
    color:#738078;
    font-size:12px;
    line-height:1.65;
}

.gsm-alumni-filter-v224{
    margin-bottom:28px;
    padding:16px;
    border:1px solid #e0e8e2;
    border-radius:18px;
    background:#fff;
    box-shadow:0 10px 30px rgba(20,52,36,.045);
}
.gsm-alumni-filter-v224__grid{
    display:grid;
    grid-template-columns:2fr 1fr 1fr auto;
    gap:10px;
    align-items:end;
}
.gsm-alumni-filter-v224 label{
    display:block;
    margin-bottom:5px;
    color:#435148;
    font-size:10px;
    font-weight:850;
}
.gsm-alumni-filter-v224 input,
.gsm-alumni-filter-v224 select{
    width:100%;
    height:43px;
    padding:0 12px;
    border:1px solid #dbe4de;
    border-radius:11px;
    background:#fff;
    color:#2f3e35;
    outline:none;
}
.gsm-alumni-filter-v224 input:focus,
.gsm-alumni-filter-v224 select:focus{
    border-color:#6faa83;
    box-shadow:0 0 0 3px rgba(22,130,74,.08);
}
.gsm-alumni-filter-v224__actions{
    display:flex;
    align-items:center;
    gap:8px;
}
.gsm-alumni-filter-v224__actions button{
    height:43px;
    padding:0 19px;
    border:0;
    border-radius:11px;
    background:#0e623a;
    color:#fff;
    font-size:11px;
    font-weight:850;
    cursor:pointer;
}
.gsm-alumni-filter-v224__actions a{
    color:#69776e;
    font-size:11px;
    font-weight:750;
    text-decoration:none;
}

.gsm-alumni-grid-v224{
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:22px;
}
.gsm-alumni-card-v224{
    overflow:hidden;
    border:1px solid #e2e8e4;
    border-radius:22px;
    background:#fff;
    box-shadow:0 12px 32px rgba(20,60,42,.045);
    transition:transform .18s ease,box-shadow .18s ease;
}
.gsm-alumni-card-v224:hover{
    transform:translateY(-3px);
    box-shadow:0 18px 40px rgba(20,60,42,.08);
}
.gsm-alumni-card-v224__photo{
    display:grid;
    place-items:center;
    height:270px;
    overflow:hidden;
    background:linear-gradient(145deg,#e8f5ec,#f9ead3);
    text-decoration:none;
}
.gsm-alumni-card-v224__photo img{
    width:100%;
    height:100%;
    object-fit:cover;
}
.gsm-alumni-card-v224__photo>span{
    color:#0e623a;
    font-size:72px;
    font-weight:900;
}
.gsm-alumni-card-v224__body{
    padding:20px;
}
.gsm-alumni-card-v224__body>small{
    color:#16824a;
    font-size:9px;
    font-weight:900;
    letter-spacing:.75px;
}
.gsm-alumni-card-v224__body h3{
    margin:7px 0 8px;
    font-size:22px;
}
.gsm-alumni-card-v224__body h3 a{
    color:#1d2d23;
    text-decoration:none;
}
.gsm-alumni-card-v224__body>p{
    min-height:42px;
    margin:0;
    color:#6b7970;
    font-size:12px;
    line-height:1.6;
}
.gsm-alumni-card-v224__meta{
    display:flex;
    gap:7px;
    flex-wrap:wrap;
    margin:13px 0;
}
.gsm-alumni-card-v224__meta span{
    padding:5px 8px;
    border-radius:999px;
    background:#f1f7f3;
    color:#557064;
    font-size:9px;
    font-weight:750;
}
.gsm-alumni-card-v224__link{
    color:#0e623a;
    font-size:11px;
    font-weight:850;
    text-decoration:none;
}
.gsm-alumni-empty-v224{
    padding:56px 24px;
    border:1px dashed #c9d9cd;
    border-radius:22px;
    background:#fff;
    text-align:center;
}
.gsm-alumni-empty-v224__mark{
    display:grid;
    place-items:center;
    width:58px;
    height:58px;
    margin:0 auto 13px;
    border-radius:18px;
    background:#eaf5ee;
    color:#0e623a;
    font-weight:900;
}
.gsm-alumni-empty-v224 h3{
    margin:0 0 7px;
    color:#26372d;
}
.gsm-alumni-empty-v224 p{
    max-width:620px;
    margin:0 auto 20px;
    color:#748179;
    font-size:12px;
    line-height:1.65;
}
.gsm-alumni-pagination-v224{
    margin-top:30px;
    text-align:center;
}

@media(max-width:900px){
    .gsm-alumni-stats-v225__grid{
        grid-template-columns:repeat(2,minmax(0,1fr))!important;
    }
    .gsm-alumni-stat-v225:nth-child(2){
        border-right:0!important;
    }
    .gsm-alumni-stat-v225:nth-child(-n+2){
        border-bottom:1px solid rgba(255,255,255,.16)!important;
    }
    .gsm-alumni-grid-v224{
        grid-template-columns:repeat(2,minmax(0,1fr));
    }
    .gsm-alumni-filter-v224__grid{
        grid-template-columns:1fr 1fr;
    }
    .gsm-alumni-directory-v224__head{
        align-items:flex-start;
        flex-direction:column;
    }
}
@media(max-width:620px){
    .gsm-alumni-stats-v225{
        padding:16px 0;
    }
    .gsm-alumni-stats-v225__grid{
        grid-template-columns:1fr!important;
        width:min(100% - 30px,1180px)!important;
    }
    .gsm-alumni-stat-v225{
        min-height:96px!important;
        padding:17px 8px!important;
        border-right:0!important;
        border-bottom:1px solid rgba(255,255,255,.14)!important;
    }
    .gsm-alumni-stat-v225:last-child{
        border-bottom:0!important;
    }
    .gsm-alumni-grid-v224,
    .gsm-alumni-filter-v224__grid{
        grid-template-columns:1fr;
    }
    .gsm-alumni-filter-v224__actions{
        flex-wrap:wrap;
    }
    .gsm-alumni-filter-v224__actions button{
        width:100%;
    }
}
CSS);
?>
