<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $model->title;
$photos = $model->photos;
?>

<section class="doc-album-hero-v22">
    <div class="container">
        <a class="doc-album-back-v22" href="<?= Url::to(['/documentation/index']) ?>">← Dokumentasi Pengabdian</a>

        <div class="doc-album-hero-v22__meta">
            <span><?= Html::encode($model->region ? $model->region->label : 'Sumatera Utara') ?></span>
            <span>·</span>
            <span><?= (int) $model->year ?></span>
            <?php if ($model->batch): ?>
                <span>·</span>
                <span>Batch <?= (int) $model->batch->batch_number ?></span>
            <?php endif; ?>
        </div>

        <h1><?= Html::encode($model->title) ?></h1>
        <p><?= nl2br(Html::encode($model->description ?: 'Dokumentasi kegiatan pengabdian Sumut Mengajar.')) ?></p>

        <div class="doc-album-count-v22"><b><?= count($photos) ?></b> foto dokumentasi</div>
    </div>
</section>

<section class="section doc-album-v22">
    <div class="container">
        <?php if (!$photos): ?>
            <div class="doc-public-empty-v22">
                <h3>Album ini belum memiliki foto.</h3>
            </div>
        <?php else: ?>
            <div class="doc-gallery-grid-v22" id="docGalleryV22">
                <?php foreach ($photos as $index => $photo): ?>
                    <?php
                        $thumbUrl = Yii::$app->request->baseUrl . '/' . ltrim($photo->thumb_path ?: $photo->file_path, '/');
                        $fullUrl = Yii::$app->request->baseUrl . '/' . ltrim($photo->file_path, '/');
                        $caption = $photo->caption ?: $model->title;
                    ?>
                    <button
                        type="button"
                        class="doc-gallery-item-v22 <?= $index === 0 ? 'is-featured' : '' ?>"
                        data-index="<?= (int) $index ?>"
                        data-full="<?= Html::encode($fullUrl) ?>"
                        data-caption="<?= Html::encode($caption) ?>"
                    >
                        <img src="<?= Html::encode($thumbUrl) ?>" alt="<?= Html::encode($caption) ?>" loading="lazy">
                        <span class="doc-gallery-item-v22__overlay">
                            <span><i class="fa fa-search-plus"></i> Lihat Foto</span>
                        </span>
                        <?php if ($photo->caption): ?><span class="doc-gallery-item-v22__caption"><?= Html::encode($photo->caption) ?></span><?php endif; ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<div class="doc-lightbox-v22" id="docLightboxV22" aria-hidden="true">
    <div class="doc-lightbox-v22__backdrop" data-doc-close></div>
    <div class="doc-lightbox-v22__dialog">
        <button class="doc-lightbox-v22__close" type="button" data-doc-close aria-label="Tutup">×</button>
        <button class="doc-lightbox-v22__nav is-prev" type="button" data-doc-prev aria-label="Foto sebelumnya">‹</button>
        <div class="doc-lightbox-v22__stage">
            <img id="docLightboxImageV22" alt="Dokumentasi pengabdian">
            <div class="doc-lightbox-v22__info">
                <span id="docLightboxCounterV22"></span>
                <p id="docLightboxCaptionV22"></p>
            </div>
        </div>
        <button class="doc-lightbox-v22__nav is-next" type="button" data-doc-next aria-label="Foto berikutnya">›</button>
    </div>
</div>

<?php
$this->registerCss(<<<'CSS'
.doc-album-hero-v22{padding:64px 0 58px;background:linear-gradient(135deg,#f2f8f3 0%,#fff8e9 100%)}.doc-album-hero-v22 .container{max-width:1020px}.doc-album-back-v22{display:inline-block;margin-bottom:20px;color:#5d6d63;text-decoration:none;font-weight:700}.doc-album-hero-v22__meta{display:flex;gap:7px;flex-wrap:wrap;color:#16824a;font-size:11px;font-weight:850;letter-spacing:.7px;text-transform:uppercase}.doc-album-hero-v22 h1{max-width:850px;margin:9px 0 12px;color:#1e3024;font-size:clamp(38px,5vw,64px);line-height:1.04;letter-spacing:-2px}.doc-album-hero-v22 p{max-width:760px;margin:0;color:#67766d;font-size:15px;line-height:1.75}.doc-album-count-v22{display:inline-flex;align-items:baseline;gap:5px;margin-top:20px;padding:9px 13px;border-radius:999px;background:#fff;color:#506057;font-size:12px;box-shadow:0 8px 20px rgba(18,42,28,.05)}.doc-album-count-v22 b{color:#0e623a;font-size:17px}.doc-album-v22{padding-top:48px;padding-bottom:90px}
.doc-gallery-grid-v22{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));grid-auto-flow:dense;gap:14px}.doc-gallery-item-v22{position:relative;display:block;padding:0;border:0;border-radius:16px;aspect-ratio:1/1;background:#edf2ef;overflow:hidden;cursor:pointer;text-align:left}.doc-gallery-item-v22.is-featured{grid-column:span 2;grid-row:span 2}.doc-gallery-item-v22 img{display:block;width:100%;height:100%;object-fit:cover;transition:transform .3s ease}.doc-gallery-item-v22:hover img{transform:scale(1.035)}.doc-gallery-item-v22__overlay{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:linear-gradient(to top,rgba(6,24,14,.45),transparent 55%);opacity:0;transition:.2s ease}.doc-gallery-item-v22__overlay span{padding:8px 12px;border-radius:999px;background:rgba(8,27,16,.72);color:#fff;font-size:11px;font-weight:800;backdrop-filter:blur(4px)}.doc-gallery-item-v22:hover .doc-gallery-item-v22__overlay{opacity:1}.doc-gallery-item-v22__caption{position:absolute;left:12px;right:12px;bottom:11px;color:#fff;font-size:11px;font-weight:700;text-shadow:0 1px 8px rgba(0,0,0,.65);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.doc-lightbox-v22{position:fixed;inset:0;z-index:99999;display:none;align-items:center;justify-content:center;padding:22px}.doc-lightbox-v22.is-open{display:flex}.doc-lightbox-v22__backdrop{position:absolute;inset:0;background:rgba(5,19,11,.9);backdrop-filter:blur(7px)}.doc-lightbox-v22__dialog{position:relative;z-index:1;display:flex;align-items:center;justify-content:center;width:min(1240px,97vw);height:92vh}.doc-lightbox-v22__stage{display:flex;flex-direction:column;align-items:center;justify-content:center;max-width:calc(100% - 110px);height:100%}.doc-lightbox-v22__stage img{display:block;max-width:100%;max-height:80vh;border-radius:12px;box-shadow:0 30px 90px rgba(0,0,0,.45)}.doc-lightbox-v22__info{width:100%;max-width:900px;margin-top:10px;color:#fff;text-align:center}.doc-lightbox-v22__info span{font-size:10px;color:#b8c8be;font-weight:800}.doc-lightbox-v22__info p{margin:4px 0 0;line-height:1.55}.doc-lightbox-v22__close{position:absolute;right:2px;top:2px;width:42px;height:42px;border:0;border-radius:50%;background:rgba(255,255,255,.12);color:#fff;font-size:27px}.doc-lightbox-v22__nav{width:48px;height:48px;border:0;border-radius:50%;background:rgba(255,255,255,.12);color:#fff;font-size:34px;line-height:1}.doc-lightbox-v22__nav:hover,.doc-lightbox-v22__close:hover{background:rgba(255,255,255,.2)}
body.doc-lightbox-open-v22{overflow:hidden}.doc-public-empty-v22{text-align:center;padding:70px 20px;color:#78857d}
@media(max-width:800px){.doc-gallery-grid-v22{grid-template-columns:repeat(2,minmax(0,1fr))}.doc-lightbox-v22__dialog{width:100%}.doc-lightbox-v22__stage{max-width:calc(100% - 82px)}.doc-lightbox-v22__nav{width:36px;height:36px;font-size:27px}.doc-album-hero-v22 h1{letter-spacing:-1px}}
@media(max-width:520px){.doc-gallery-grid-v22{gap:8px}.doc-gallery-item-v22.is-featured{grid-column:span 2}.doc-lightbox-v22{padding:10px}.doc-lightbox-v22__stage{max-width:100%;width:100%}.doc-lightbox-v22__nav{position:absolute;z-index:3;top:50%}.doc-lightbox-v22__nav.is-prev{left:4px}.doc-lightbox-v22__nav.is-next{right:4px}.doc-lightbox-v22__close{right:4px;top:4px;z-index:3}}
CSS);

$this->registerJs(<<<'JS'
(function(){
    const modal=document.getElementById('docLightboxV22');
    if(!modal)return;
    const items=Array.from(document.querySelectorAll('.doc-gallery-item-v22'));
    const image=document.getElementById('docLightboxImageV22');
    const caption=document.getElementById('docLightboxCaptionV22');
    const counter=document.getElementById('docLightboxCounterV22');
    let current=0;

    function show(index){
        if(!items.length)return;
        current=(index+items.length)%items.length;
        const item=items[current];
        image.src=item.dataset.full;
        caption.textContent=item.dataset.caption||'';
        counter.textContent=(current+1)+' / '+items.length;
    }
    function open(index){
        show(index);
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden','false');
        document.body.classList.add('doc-lightbox-open-v22');
    }
    function close(){
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden','true');
        document.body.classList.remove('doc-lightbox-open-v22');
        image.removeAttribute('src');
    }

    items.forEach((item,index)=>item.addEventListener('click',()=>open(index)));
    document.addEventListener('click',function(e){
        if(e.target.closest('[data-doc-close]'))close();
        if(e.target.closest('[data-doc-prev]'))show(current-1);
        if(e.target.closest('[data-doc-next]'))show(current+1);
    });
    document.addEventListener('keydown',function(e){
        if(!modal.classList.contains('is-open'))return;
        if(e.key==='Escape')close();
        if(e.key==='ArrowLeft')show(current-1);
        if(e.key==='ArrowRight')show(current+1);
    });
})();
JS);
?>
