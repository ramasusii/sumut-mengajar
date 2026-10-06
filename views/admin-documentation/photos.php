<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Kelola Foto — ' . $model->title;
?>

<div class="doc-photo-header-v22">
    <div>
        <div class="doc-photo-kicker-v22"><?= Html::encode($model->region ? $model->region->label : '-') ?> · <?= (int) $model->year ?></div>
        <h3><?= Html::encode($model->title) ?></h3>
        <p><?= Html::encode($model->description ?: 'Kelola foto dan cover album dokumentasi.') ?></p>
    </div>
    <div class="doc-photo-header-actions-v22">
        <?= Html::a('<i class="fa fa-pencil"></i> Edit Album', ['update', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
        <?= Html::a('<i class="fa fa-arrow-left"></i> Semua Album', ['index'], ['class' => 'btn btn-default']) ?>
        <?php if ($model->status === 'published'): ?>
            <?= Html::a('<i class="fa fa-external-link"></i> Lihat Website', ['/documentation/view', 'slug' => $model->slug], ['class' => 'btn btn-success', 'target' => '_blank']) ?>
        <?php endif; ?>
    </div>
</div>

<div class="box box-success doc-upload-box-v22">
    <div class="box-header with-border">
        <h3 class="box-title">Tambah Foto</h3>
    </div>
    <div class="box-body">
        <?= Html::beginForm(['upload', 'id' => $model->id], 'post', ['enctype' => 'multipart/form-data', 'id' => 'docUploadFormV22']) ?>
        <label class="doc-dropzone-v22" for="documentationPhotosV22">
            <input id="documentationPhotosV22" type="file" name="photos[]" accept=".jpg,.jpeg,.png,.webp" multiple>
            <span class="doc-dropzone-v22__icon"><i class="fa fa-cloud-upload"></i></span>
            <strong>Pilih foto dokumentasi</strong>
            <span>Bisa pilih beberapa foto sekaligus · maksimal <?= (int) $maxUploadFiles ?> foto per unggahan · <?= (int) $maxUploadKb ?> KB per foto.</span>
            <small>Foto akan dioptimalkan otomatis untuk website agar galeri tetap cepat.</small>
        </label>
        <div id="docUploadSelectionV22" class="doc-upload-selection-v22">Belum ada foto dipilih.</div>
        <button class="btn btn-success" type="submit"><i class="fa fa-upload"></i> Unggah Foto</button>
        <?= Html::endForm() ?>
    </div>
</div>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Isi Album</h3>
        <div class="box-tools"><span class="label label-default"><?= count($photos) ?> foto</span></div>
    </div>
    <div class="box-body">
        <?php if (!$photos): ?>
            <div class="doc-photo-empty-v22">
                <i class="fa fa-picture-o"></i>
                <h4>Album ini belum memiliki foto.</h4>
                <p>Unggah foto pertama. Foto pertama otomatis menjadi cover album.</p>
            </div>
        <?php else: ?>
            <div class="doc-photo-grid-v22">
                <?php foreach ($photos as $index => $photo): ?>
                    <?php
                        $thumbUrl = Yii::$app->request->baseUrl . '/' . ltrim($photo->thumb_path ?: $photo->file_path, '/');
                        $fullUrl = Yii::$app->request->baseUrl . '/' . ltrim($photo->file_path, '/');
                    ?>
                    <article class="doc-photo-card-v22 <?= $photo->is_cover ? 'is-cover' : '' ?>">
                        <button
                            class="doc-photo-preview-v22 js-admin-photo-preview"
                            type="button"
                            data-url="<?= Html::encode($fullUrl) ?>"
                            data-caption="<?= Html::encode($photo->caption ?: $photo->original_name) ?>"
                        >
                            <img src="<?= Html::encode($thumbUrl) ?>" alt="<?= Html::encode($photo->caption ?: $model->title) ?>">
                            <span><i class="fa fa-search-plus"></i> Lihat</span>
                        </button>

                        <div class="doc-photo-card-body-v22">
                            <div class="doc-photo-card-top-v22">
                                <span class="doc-photo-number-v22">#<?= (int) $index + 1 ?></span>
                                <?php if ($photo->is_cover): ?><span class="doc-photo-cover-badge-v22"><i class="fa fa-star"></i> Cover</span><?php endif; ?>
                            </div>

                            <?= Html::beginForm(['photo-caption', 'id' => $model->id, 'photoId' => $photo->id], 'post') ?>
                                <?= Html::textarea('caption', $photo->caption, [
                                    'class' => 'form-control input-sm',
                                    'rows' => 2,
                                    'placeholder' => 'Tambahkan keterangan foto...',
                                ]) ?>
                                <button class="btn btn-default btn-xs" style="margin-top:7px" type="submit"><i class="fa fa-save"></i> Simpan Keterangan</button>
                            <?= Html::endForm() ?>

                            <div class="doc-photo-card-actions-v22">
                                <?php if (!$photo->is_cover): ?>
                                    <?= Html::a('<i class="fa fa-star-o"></i> Jadikan Cover', ['set-cover', 'id' => $model->id, 'photoId' => $photo->id], [
                                        'class' => 'btn btn-success btn-xs',
                                        'data' => ['method' => 'post'],
                                    ]) ?>
                                <?php endif; ?>

                                <div class="btn-group">
                                    <?= Html::a('<i class="fa fa-arrow-left"></i>', ['move-photo', 'id' => $model->id, 'photoId' => $photo->id, 'direction' => 'up'], [
                                        'class' => 'btn btn-default btn-xs',
                                        'data' => ['method' => 'post'],
                                        'title' => 'Geser ke depan',
                                    ]) ?>
                                    <?= Html::a('<i class="fa fa-arrow-right"></i>', ['move-photo', 'id' => $model->id, 'photoId' => $photo->id, 'direction' => 'down'], [
                                        'class' => 'btn btn-default btn-xs',
                                        'data' => ['method' => 'post'],
                                        'title' => 'Geser ke belakang',
                                    ]) ?>
                                </div>

                                <?= Html::a('<i class="fa fa-trash"></i>', ['delete-photo', 'id' => $model->id, 'photoId' => $photo->id], [
                                    'class' => 'btn btn-danger btn-xs pull-right',
                                    'data' => ['method' => 'post', 'confirm' => 'Hapus foto ini dari album?'],
                                ]) ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="doc-admin-lightbox-v22" id="docAdminLightboxV22" aria-hidden="true">
    <div class="doc-admin-lightbox-v22__backdrop" data-doc-admin-close></div>
    <div class="doc-admin-lightbox-v22__dialog">
        <button type="button" class="doc-admin-lightbox-v22__close" data-doc-admin-close>×</button>
        <img id="docAdminLightboxImageV22" alt="Pratinjau dokumentasi">
        <p id="docAdminLightboxCaptionV22"></p>
    </div>
</div>

<?php
$this->registerCss(<<<'CSS'
.doc-photo-header-v22{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;padding:18px 20px;margin-bottom:16px;border:1px solid #e4eae6;border-radius:14px;background:#fff;box-shadow:0 8px 24px rgba(18,42,28,.05)}.doc-photo-kicker-v22{font-size:10px;letter-spacing:.8px;text-transform:uppercase;color:#16824a;font-weight:850}.doc-photo-header-v22 h3{margin:4px 0 5px;font-weight:800}.doc-photo-header-v22 p{margin:0;color:#77847c}.doc-photo-header-actions-v22{display:flex;gap:7px;flex-wrap:wrap}
.doc-dropzone-v22{position:relative;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:190px;padding:26px;border:2px dashed #b8cfc0;border-radius:14px;background:#f8fbf9;text-align:center;cursor:pointer;transition:.18s ease}.doc-dropzone-v22:hover{border-color:#16824a;background:#f2faf5}.doc-dropzone-v22 input{position:absolute;width:1px;height:1px;opacity:0}.doc-dropzone-v22__icon{display:flex;align-items:center;justify-content:center;width:52px;height:52px;margin-bottom:9px;border-radius:50%;background:#e6f5ec;color:#16824a;font-size:21px}.doc-dropzone-v22 strong{font-size:16px;color:#29392f}.doc-dropzone-v22 span:not(.doc-dropzone-v22__icon){margin-top:5px;color:#6f7c74}.doc-dropzone-v22 small{margin-top:7px;color:#87938b}.doc-upload-selection-v22{margin:10px 0;color:#66746b;font-size:12px}.doc-upload-selection-v22.is-error{color:#b23c32;font-weight:800}
.doc-photo-grid-v22{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}.doc-photo-card-v22{overflow:hidden;border:1px solid #e4e9e6;border-radius:13px;background:#fff}.doc-photo-card-v22.is-cover{border-color:#8dccaa;box-shadow:0 0 0 2px rgba(22,130,74,.08)}.doc-photo-preview-v22{position:relative;display:block;width:100%;padding:0;border:0;background:#edf2ef;aspect-ratio:4/3;overflow:hidden;cursor:pointer}.doc-photo-preview-v22 img{display:block;width:100%;height:100%;object-fit:cover;transition:transform .2s ease}.doc-photo-preview-v22 span{position:absolute;left:50%;top:50%;transform:translate(-50%,-40%);padding:7px 11px;border-radius:999px;background:rgba(10,31,19,.76);color:#fff;font-size:11px;font-weight:800;opacity:0;transition:.18s ease}.doc-photo-preview-v22:hover img{transform:scale(1.025)}.doc-photo-preview-v22:hover span{opacity:1;transform:translate(-50%,-50%)}.doc-photo-card-body-v22{padding:12px}.doc-photo-card-top-v22{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}.doc-photo-number-v22{font-size:11px;color:#87938c;font-weight:800}.doc-photo-cover-badge-v22{padding:4px 8px;border-radius:999px;background:#e6f5ec;color:#147443;font-size:10px;font-weight:850}.doc-photo-card-actions-v22{display:flex;align-items:center;gap:6px;margin-top:10px;padding-top:10px;border-top:1px solid #edf1ee}.doc-photo-card-actions-v22 .pull-right{margin-left:auto}.doc-photo-empty-v22{text-align:center;padding:45px 20px;color:#7b887f}.doc-photo-empty-v22>i{font-size:40px;color:#adc2b4}
.doc-admin-lightbox-v22{position:fixed;inset:0;z-index:99999;display:none;align-items:center;justify-content:center;padding:28px}.doc-admin-lightbox-v22.is-open{display:flex}.doc-admin-lightbox-v22__backdrop{position:absolute;inset:0;background:rgba(8,24,15,.82);backdrop-filter:blur(4px)}.doc-admin-lightbox-v22__dialog{position:relative;z-index:1;max-width:min(1080px,95vw);max-height:92vh;text-align:center}.doc-admin-lightbox-v22__dialog img{display:block;max-width:100%;max-height:82vh;margin:auto;border-radius:12px;box-shadow:0 24px 80px rgba(0,0,0,.35)}.doc-admin-lightbox-v22__dialog p{margin:10px 48px 0;color:#fff}.doc-admin-lightbox-v22__close{position:absolute;right:-12px;top:-12px;width:38px;height:38px;border:0;border-radius:50%;background:#fff;color:#25342a;font-size:24px;z-index:2}
@media(max-width:1000px){.doc-photo-grid-v22{grid-template-columns:repeat(2,minmax(0,1fr))}.doc-photo-header-v22{flex-direction:column}}
@media(max-width:650px){.doc-photo-grid-v22{grid-template-columns:1fr}}
CSS);

$this->registerJs(<<<'JS'
(function(){
    const input=document.getElementById('documentationPhotosV22');
    const selection=document.getElementById('docUploadSelectionV22');
    if(input && selection){
        input.addEventListener('change',function(){
            const files=Array.from(input.files||[]);
            const maxBytes=200*1024;

            if(!files.length){
                selection.textContent='Belum ada foto dipilih.';
                selection.classList.remove('is-error');
                return;
            }

            const oversized=files.find(file=>file.size>maxBytes);
            if(oversized){
                const sizeKb=Math.ceil(oversized.size/1024);
                input.value='';
                selection.textContent='File "'+oversized.name+'" berukuran '+sizeKb+' KB. Maksimal 200 KB per foto.';
                selection.classList.add('is-error');
                return;
            }

            selection.classList.remove('is-error');
            selection.textContent=files.length+' foto dipilih: '+files.slice(0,4).map(f=>f.name).join(', ')+(files.length>4?' dan lainnya':'');
        });
    }

    const modal=document.getElementById('docAdminLightboxV22');
    const image=document.getElementById('docAdminLightboxImageV22');
    const caption=document.getElementById('docAdminLightboxCaptionV22');
    function close(){
        if(!modal)return;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden','true');
        image.removeAttribute('src');
    }
    document.addEventListener('click',function(e){
        const btn=e.target.closest('.js-admin-photo-preview');
        if(btn && modal){
            image.src=btn.dataset.url;
            caption.textContent=btn.dataset.caption||'';
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden','false');
            return;
        }
        if(e.target.closest('[data-doc-admin-close]')) close();
    });
    document.addEventListener('keydown',function(e){if(e.key==='Escape')close();});
})();
JS);
?>
