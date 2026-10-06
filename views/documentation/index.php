<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Dokumentasi Pengabdian';

$grouped = [];
foreach ($models as $model) {
    $grouped[(int) $model->year][] = $model;
}
?>

<section class="page-hero doc-public-hero-v22">
    <div class="container">
        <span class="section-kicker">DOKUMENTASI PENGABDIAN</span>
        <h1>Jejak pengabdian dari berbagai daerah.</h1>
        <p>Kumpulan cerita visual perjalanan Sumut Mengajar bersama sekolah, masyarakat, relawan, dan anak-anak di Sumatera Utara.</p>
    </div>
</section>

<section class="section doc-public-v22">
    <div class="container">
        <div class="doc-public-filter-v22">
            <div class="doc-public-filter-v22__copy">
                <b>Jelajahi Dokumentasi</b>
                <span>Filter berdasarkan daerah atau tahun kegiatan.</span>
            </div>

            <?= Html::beginForm(['/documentation/index'], 'get', ['class' => 'doc-public-filter-v22__form']) ?>
                <select name="daerah" class="doc-public-select-v22">
                    <option value="">Semua Daerah</option>
                    <?php foreach ($regions as $region): ?>
                        <option value="<?= Html::encode($region->slug) ?>" <?= $regionSlug === $region->slug ? 'selected' : '' ?>>
                            <?= Html::encode($region->label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="tahun" class="doc-public-select-v22">
                    <option value="">Semua Tahun</option>
                    <?php foreach ($years as $yearOption): ?>
                        <option value="<?= (int) $yearOption ?>" <?= $year === (int) $yearOption ? 'selected' : '' ?>>
                            <?= (int) $yearOption ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button class="doc-public-filter-button-v22" type="submit">Tampilkan</button>

                <?php if ($regionSlug !== '' || $year > 0): ?>
                    <a class="doc-public-reset-v22" href="<?= Url::to(['/documentation/index']) ?>">Reset</a>
                <?php endif; ?>
            <?= Html::endForm() ?>
        </div>

        <?php if (!$models): ?>
            <div class="doc-public-empty-v22">
                <div class="doc-public-empty-v22__icon">✦</div>
                <h3>Belum ada dokumentasi pada pilihan ini.</h3>
                <p>Coba pilih daerah atau tahun lainnya.</p>
            </div>
        <?php else: ?>
            <?php foreach ($grouped as $groupYear => $albums): ?>
                <section class="doc-year-section-v22">
                    <div class="doc-year-heading-v22">
                        <div>
                            <span>TAHUN PENGABDIAN</span>
                            <h2><?= (int) $groupYear ?></h2>
                        </div>
                        <div class="doc-year-line-v22"></div>
                    </div>

                    <div class="doc-public-grid-v22">
                        <?php foreach ($albums as $album): ?>
                            <?php
                                $cover = $album->getDisplayCoverPhoto();
                                $coverUrl = $cover
                                    ? Yii::$app->request->baseUrl . '/' . ltrim($cover->thumb_path ?: $cover->file_path, '/')
                                    : null;
                            ?>
                            <article class="doc-public-card-v22">
                                <a class="doc-public-card-v22__media" href="<?= Url::to(['/documentation/view', 'slug' => $album->slug]) ?>">
                                    <?php if ($coverUrl): ?>
                                        <img src="<?= Html::encode($coverUrl) ?>" alt="<?= Html::encode($album->title) ?>" loading="lazy">
                                    <?php else: ?>
                                        <div class="doc-public-card-placeholder-v22">Sumut Mengajar</div>
                                    <?php endif; ?>

                                    <span class="doc-public-card-v22__count">
                                        <?= (int) $album->getPhotoCount() ?> foto
                                    </span>
                                </a>

                                <div class="doc-public-card-v22__body">
                                    <div class="doc-public-card-v22__eyebrow">
                                        <?= Html::encode($album->region ? $album->region->label : 'Sumatera Utara') ?>
                                        <span>·</span>
                                        <?= (int) $album->year ?>
                                    </div>
                                    <h3><?= Html::encode($album->title) ?></h3>
                                    <p><?= Html::encode($album->description ?: 'Dokumentasi kegiatan pengabdian Sumut Mengajar.') ?></p>

                                    <div class="doc-public-card-v22__footer">
                                        <?php if ($album->batch): ?>
                                            <span>Batch <?= (int) $album->batch->batch_number ?></span>
                                        <?php else: ?>
                                            <span>Pengabdian Sumut Mengajar</span>
                                        <?php endif; ?>

                                        <a href="<?= Url::to(['/documentation/view', 'slug' => $album->slug]) ?>">Lihat Album <b>→</b></a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>

            <div class="doc-public-pagination-v22">
                <?= LinkPager::widget(['pagination' => $pagination]) ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
$this->registerCss(<<<'CSS'
.doc-public-hero-v22 .container{max-width:1040px}.doc-public-hero-v22 h1{max-width:760px}.doc-public-hero-v22 p{max-width:760px}
.doc-public-v22{padding-top:54px;padding-bottom:90px}.doc-public-filter-v22{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:18px 20px;margin-bottom:45px;border:1px solid #e3eae5;border-radius:18px;background:#fff;box-shadow:0 12px 32px rgba(20,52,36,.045)}.doc-public-filter-v22__copy b,.doc-public-filter-v22__copy span{display:block}.doc-public-filter-v22__copy b{color:#233329;font-size:15px}.doc-public-filter-v22__copy span{margin-top:3px;color:#819087;font-size:12px}.doc-public-filter-v22__form{display:flex;align-items:center;gap:8px}.doc-public-select-v22{min-width:170px;height:42px;padding:0 38px 0 13px;border:1px solid #dae3dd;border-radius:11px;background:#fff;color:#34443a}.doc-public-filter-button-v22{height:42px;padding:0 17px;border:0;border-radius:11px;background:#0e623a;color:#fff;font-weight:800}.doc-public-reset-v22{padding:10px;color:#67756c;text-decoration:none}
.doc-year-section-v22{margin-bottom:52px}.doc-year-heading-v22{display:flex;align-items:flex-end;gap:18px;margin-bottom:18px}.doc-year-heading-v22 span{font-size:9px;letter-spacing:1px;color:#16824a;font-weight:850}.doc-year-heading-v22 h2{margin:2px 0 0;font-size:34px;line-height:1;color:#203027}.doc-year-line-v22{height:1px;flex:1;background:#e5ebe7;margin-bottom:5px}
.doc-public-grid-v22{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:24px}.doc-public-card-v22{overflow:hidden;border:1px solid #e4eae6;border-radius:22px;background:#fff;box-shadow:0 14px 36px rgba(18,42,28,.05);transition:transform .18s ease,box-shadow .18s ease}.doc-public-card-v22:hover{transform:translateY(-3px);box-shadow:0 20px 42px rgba(18,42,28,.085)}.doc-public-card-v22__media{position:relative;display:block;aspect-ratio:4/3;overflow:hidden;background:#eef3ef}.doc-public-card-v22__media img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .3s ease}.doc-public-card-v22:hover .doc-public-card-v22__media img{transform:scale(1.035)}.doc-public-card-placeholder-v22{height:100%;display:flex;align-items:center;justify-content:center;color:#9aaa9f;font-weight:800}.doc-public-card-v22__count{position:absolute;right:12px;bottom:12px;padding:7px 10px;border-radius:999px;background:rgba(12,36,22,.78);color:#fff;font-size:10px;font-weight:800;backdrop-filter:blur(5px)}.doc-public-card-v22__body{padding:20px}.doc-public-card-v22__eyebrow{display:flex;gap:6px;flex-wrap:wrap;color:#16824a;font-size:10px;font-weight:850;letter-spacing:.55px;text-transform:uppercase}.doc-public-card-v22__body h3{margin:7px 0 8px;color:#1f3025;font-size:20px;line-height:1.25}.doc-public-card-v22__body p{min-height:58px;margin:0;color:#6f7d74;font-size:12px;line-height:1.65}.doc-public-card-v22__footer{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:17px;padding-top:14px;border-top:1px solid #edf1ee;font-size:11px}.doc-public-card-v22__footer>span{color:#8a968f}.doc-public-card-v22__footer a{color:#0e623a;font-weight:850;text-decoration:none}.doc-public-pagination-v22{text-align:center;margin-top:28px}.doc-public-empty-v22{text-align:center;padding:72px 20px;border:1px dashed #d9e4dc;border-radius:18px;background:#fbfdfb}.doc-public-empty-v22__icon{font-size:32px;color:#6fac83}.doc-public-empty-v22 h3{margin:10px 0 5px}.doc-public-empty-v22 p{color:#7d8981}
@media(max-width:960px){.doc-public-grid-v22{grid-template-columns:repeat(2,minmax(0,1fr))}.doc-public-filter-v22{align-items:flex-start;flex-direction:column}.doc-public-filter-v22__form{width:100%;flex-wrap:wrap}.doc-public-select-v22{flex:1}}
@media(max-width:640px){.doc-public-grid-v22{grid-template-columns:1fr}.doc-public-filter-v22__form{display:grid;grid-template-columns:1fr}.doc-public-select-v22{width:100%}.doc-public-filter-button-v22{width:100%}.doc-public-card-v22__body p{min-height:0}}
CSS);
?>
