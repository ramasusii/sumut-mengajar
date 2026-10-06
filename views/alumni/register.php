<?php
use app\models\AlumniPublication;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->title='Pendaftaran Alumni';
?>
<section class="page-hero alumni-register-hero"><div class="container"><span class="section-kicker">DATABASE ALUMNI</span><h1>Ceritakan perjalananmu setelah mengabdi.</h1><p>Form ini untuk alumni Sumut Mengajar dari batch terdahulu yang belum tercatat. Data akan diverifikasi sebelum tampil di website.</p></div></section>
<section class="section soft"><div class="container alumni-register-wrap">
<?php if(Yii::$app->session->hasFlash('success')): ?><div class="alumni-success"><?= Html::encode(Yii::$app->session->getFlash('success')) ?></div><?php endif; ?>
<div class="alumni-register-card"><?php $form=ActiveForm::begin(['options'=>['enctype'=>'multipart/form-data']]); ?><h2>Identitas Pengabdian</h2><div class="alumni-form-grid"><?= $form->field($model,'nama_lengkap')->textInput(['placeholder'=>'Nama lengkap']) ?><?= $form->field($model,'whatsapp')->textInput(['placeholder'=>'081234567890','inputmode'=>'tel'])->label('Nomor WhatsApp Aktif') ?><?= $form->field($model,'batch_number')->input('number',['placeholder'=>'Contoh: 12'])->label('Alumni Batch') ?><?= $form->field($model,'batch_year')->input('number',['placeholder'=>'Contoh: 2022'])->label('Tahun Pengabdian') ?><?= $form->field($model,'location_name')->textInput(['placeholder'=>'Contoh: Samosir'])->label('Lokasi / Chapter Pengabdian') ?><div class="form-group alumni-passphoto-v241">
<label>Foto Profil</label>

<div class="alumni-photo-upload-v241">
    <?= Html::activeFileInput($model,'photo',[
        'class'=>'alumni-photo-input-v241',
        'accept'=>'image/jpeg,image/png',
        'id'=>'alumni-photo-v241'
    ]) ?>

    <label class="alumni-photo-picker-v241" for="alumni-photo-v241">
        <span class="alumni-photo-picker-v241__icon">+</span>
        <span>
            <b>Pilih Foto</b>
            <small>JPG atau PNG</small>
        </span>
    </label>

    <div class="alumni-photo-preview-v241" id="alumni-photo-preview-v241" hidden>
        <img id="alumni-photo-preview-image-v241" alt="Pratinjau foto profil">
        <div>
            <b>Foto siap digunakan</b>
            <button type="button" id="alumni-photo-change-v241">Ganti / Atur Ulang</button>
        </div>
    </div>
</div>

<p class="help-block">
    Unggah pas foto resmi rasio <b>3:4</b>. Atur posisi foto agar wajah terlihat jelas.
    Maksimal <b>200 KB</b>.
</p>
<p class="alumni-photo-status-v241" id="alumni-photo-status-v241"></p>
</div></div><hr><h2>Karier Saat Ini</h2><div class="alumni-form-grid"><?= $form->field($model,'current_position')->textInput(['placeholder'=>'Contoh: Software Engineer'])->label('Jabatan') ?><?= $form->field($model,'current_institution')->textInput(['placeholder'=>'Instansi / Perusahaan'])->label('Instansi / Perusahaan') ?><?= $form->field($model,'sector')->textInput(['placeholder'=>'Pendidikan, Pemerintahan, Teknologi, dll.'])->label('Sektor') ?><?= $form->field($model,'work_city')->textInput(['placeholder'=>'Kota tempat bekerja'])->label('Kota Bekerja') ?><?= $form->field($model,'instagram')->textInput(['placeholder'=>'@username']) ?><?= $form->field($model,'linkedin')->textInput(['placeholder'=>'https://linkedin.com/in/...']) ?></div><?= $form->field($model,'bio')->textarea(['rows'=>5,'placeholder'=>'Ceritakan singkat perjalanan setelah Sumut Mengajar, dampak pengabdian, atau aktivitas saat ini.'])->label('Cerita Singkat') ?><hr><h2>Karya / Skripsi / Penelitian</h2><p class="alumni-form-note">Isi jika ada. Karya lain dapat ditambahkan oleh tim setelah verifikasi.</p><div class="alumni-form-grid"><?= $form->field($model,'publication_title')->textInput(['placeholder'=>'Judul karya / skripsi / penelitian'])->label('Judul Karya') ?><?= $form->field($model,'publication_type')->dropDownList(AlumniPublication::TYPES,['prompt'=>'Pilih jenis'])->label('Jenis Karya') ?><?= $form->field($model,'publication_year')->input('number')->label('Tahun') ?><?= $form->field($model,'publication_url')->textInput(['placeholder'=>'Tautan repository / publikasi'])->label('Tautan') ?></div><?= $form->field($model,'publication_about_service')->checkbox()->label('Karya ini membahas pengalaman atau pengabdian Sumut Mengajar') ?><hr><?= $form->field($model,'consent_public')->checkbox()->label('Saya setuju data profil, riwayat pengabdian, karier, dan karya yang saya isi dapat ditampilkan pada halaman Alumni Sumut Mengajar setelah diverifikasi. Nomor WhatsApp tidak akan ditampilkan ke publik.') ?><button class="btn-primary-gsm" type="submit">Kirim Data Alumni →</button><?php ActiveForm::end(); ?></div>
</div></section>


<div class="alumni-crop-modal-v241" id="alumni-crop-modal-v241" aria-hidden="true">
    <div class="alumni-crop-modal-v241__backdrop"></div>

    <div class="alumni-crop-modal-v241__dialog" role="dialog" aria-modal="true" aria-labelledby="alumni-crop-title-v241">
        <div class="alumni-crop-modal-v241__head">
            <div>
                <small>FOTO PROFIL</small>
                <h3 id="alumni-crop-title-v241">Atur Pas Foto</h3>
            </div>
            <button type="button" class="alumni-crop-close-v241" id="alumni-crop-close-v241" aria-label="Tutup">×</button>
        </div>

        <div class="alumni-crop-modal-v241__body">
            <div class="alumni-crop-stage-v241">
                <canvas id="alumni-crop-canvas-v241" width="354" height="472"></canvas>
                <div class="alumni-crop-guide-v241" aria-hidden="true">
                    <span class="is-top"></span>
                    <span class="is-bottom"></span>
                    <span class="is-left"></span>
                    <span class="is-right"></span>
                </div>
            </div>

            <p class="alumni-crop-hint-v241">Geser foto untuk mengatur posisi wajah.</p>

            <div class="alumni-crop-zoom-v241">
                <span>−</span>
                <input id="alumni-crop-zoom-v241" type="range" min="1" max="3" value="1" step="0.01" aria-label="Perbesar foto">
                <span>+</span>
            </div>
        </div>

        <div class="alumni-crop-modal-v241__footer">
            <button type="button" class="alumni-crop-btn-v241 is-secondary" id="alumni-crop-cancel-v241">Batal</button>
            <button type="button" class="alumni-crop-btn-v241 is-primary" id="alumni-crop-use-v241">Gunakan Foto</button>
        </div>
    </div>
</div>

<?php
$this->registerCss(<<<'CSS'
.alumni-passphoto-v241 .help-block{
    margin-top:8px;
    color:#748179;
    font-size:11px;
    line-height:1.6;
}
.alumni-photo-input-v241{
    position:absolute!important;
    width:1px!important;
    height:1px!important;
    opacity:0!important;
    overflow:hidden!important;
    pointer-events:none!important;
}
.alumni-photo-picker-v241{
    display:flex;
    align-items:center;
    gap:11px;
    min-height:58px;
    padding:10px 13px;
    margin:0;
    border:1px dashed #bdd0c2;
    border-radius:12px;
    background:#f8fbf9;
    cursor:pointer;
    transition:.18s ease;
}
.alumni-photo-picker-v241:hover{
    border-color:#16824a;
    background:#f2f9f4;
}
.alumni-photo-picker-v241__icon{
    display:grid;
    place-items:center;
    width:36px;
    height:36px;
    flex:0 0 36px;
    border-radius:10px;
    background:#e7f4eb;
    color:#0e623a;
    font-size:21px;
    font-weight:500;
}
.alumni-photo-picker-v241 b,
.alumni-photo-picker-v241 small{
    display:block;
}
.alumni-photo-picker-v241 b{
    color:#2f4036;
    font-size:12px;
}
.alumni-photo-picker-v241 small{
    margin-top:2px;
    color:#859188;
    font-size:10px;
}
.alumni-photo-preview-v241{
    display:flex;
    align-items:center;
    gap:12px;
    padding:10px;
    border:1px solid #dce7df;
    border-radius:12px;
    background:#fff;
}
.alumni-photo-preview-v241[hidden]{
    display:none!important;
}
.alumni-photo-preview-v241 img{
    display:block;
    width:54px;
    height:72px;
    flex:0 0 54px;
    object-fit:cover;
    border-radius:8px;
    background:#edf2ee;
}
.alumni-photo-preview-v241 b{
    display:block;
    color:#2b3b31;
    font-size:11px;
}
.alumni-photo-preview-v241 button{
    margin-top:4px;
    padding:0;
    border:0;
    background:transparent;
    color:#0e623a;
    font-size:10px;
    font-weight:850;
    cursor:pointer;
}
.alumni-photo-status-v241{
    display:none;
    margin:7px 0 0;
    font-size:11px;
    font-weight:800;
}
.alumni-photo-status-v241.is-ok{
    display:block;
    color:#147443;
}
.alumni-photo-status-v241.is-error{
    display:block;
    color:#b23c32;
}

/* Crop modal */
.alumni-crop-modal-v241{
    position:fixed;
    inset:0;
    z-index:100000;
    display:none;
    align-items:center;
    justify-content:center;
    padding:20px;
}
.alumni-crop-modal-v241.is-open{
    display:flex;
}
.alumni-crop-modal-v241__backdrop{
    position:absolute;
    inset:0;
    background:rgba(8,24,15,.82);
    backdrop-filter:blur(5px);
}
.alumni-crop-modal-v241__dialog{
    position:relative;
    z-index:1;
    width:min(440px,96vw);
    overflow:hidden;
    border-radius:20px;
    background:#fff;
    box-shadow:0 28px 90px rgba(0,0,0,.28);
}
.alumni-crop-modal-v241__head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    padding:17px 19px;
    border-bottom:1px solid #edf1ee;
}
.alumni-crop-modal-v241__head small{
    display:block;
    color:#16824a;
    font-size:8px;
    font-weight:900;
    letter-spacing:1px;
}
.alumni-crop-modal-v241__head h3{
    margin:3px 0 0;
    color:#24342a;
    font-size:18px;
}
.alumni-crop-close-v241{
    width:34px;
    height:34px;
    padding:0;
    border:0;
    border-radius:50%;
    background:#f1f4f2;
    color:#536158;
    font-size:22px;
    line-height:1;
    cursor:pointer;
}
.alumni-crop-modal-v241__body{
    padding:18px;
}
.alumni-crop-stage-v241{
    position:relative;
    width:min(270px,76vw);
    aspect-ratio:3/4;
    margin:0 auto;
    overflow:hidden;
    border-radius:14px;
    background:#18231c;
    box-shadow:0 10px 24px rgba(21,45,29,.12);
    touch-action:none;
    cursor:grab;
}
.alumni-crop-stage-v241:active{
    cursor:grabbing;
}
.alumni-crop-stage-v241 canvas{
    display:block;
    width:100%;
    height:100%;
}
.alumni-crop-guide-v241{
    position:absolute;
    inset:0;
    pointer-events:none;
    box-shadow:inset 0 0 0 2px rgba(255,255,255,.9);
}
.alumni-crop-guide-v241 span{
    position:absolute;
    background:rgba(255,255,255,.45);
}
.alumni-crop-guide-v241 .is-top,
.alumni-crop-guide-v241 .is-bottom{
    left:0;
    right:0;
    height:1px;
}
.alumni-crop-guide-v241 .is-top{top:33.333%}
.alumni-crop-guide-v241 .is-bottom{top:66.666%}
.alumni-crop-guide-v241 .is-left,
.alumni-crop-guide-v241 .is-right{
    top:0;
    bottom:0;
    width:1px;
}
.alumni-crop-guide-v241 .is-left{left:33.333%}
.alumni-crop-guide-v241 .is-right{left:66.666%}
.alumni-crop-hint-v241{
    margin:11px 0 0;
    color:#7a877f;
    font-size:10px;
    text-align:center;
}
.alumni-crop-zoom-v241{
    display:grid;
    grid-template-columns:20px 1fr 20px;
    align-items:center;
    gap:9px;
    max-width:300px;
    margin:15px auto 0;
    color:#708078;
    font-size:17px;
    text-align:center;
}
.alumni-crop-zoom-v241 input{
    width:100%;
    accent-color:#0e623a;
}
.alumni-crop-modal-v241__footer{
    display:flex;
    justify-content:flex-end;
    gap:8px;
    padding:13px 18px 17px;
    border-top:1px solid #edf1ee;
}
.alumni-crop-btn-v241{
    min-height:40px;
    padding:0 16px;
    border-radius:10px;
    font-size:11px;
    font-weight:850;
    cursor:pointer;
}
.alumni-crop-btn-v241.is-secondary{
    border:1px solid #dce4df;
    background:#fff;
    color:#526158;
}
.alumni-crop-btn-v241.is-primary{
    border:0;
    background:#0e623a;
    color:#fff;
}
body.alumni-crop-open-v241{
    overflow:hidden;
}
@media(max-width:520px){
    .alumni-crop-modal-v241{
        padding:10px;
    }
    .alumni-crop-modal-v241__dialog{
        width:100%;
        border-radius:16px;
    }
    .alumni-crop-stage-v241{
        width:min(260px,74vw);
    }
}
CSS);

$this->registerJs(<<<'JS'
(function(){
    const input=document.getElementById('alumni-photo-v241');
    const picker=document.querySelector('.alumni-photo-picker-v241');
    const preview=document.getElementById('alumni-photo-preview-v241');
    const previewImage=document.getElementById('alumni-photo-preview-image-v241');
    const changeButton=document.getElementById('alumni-photo-change-v241');
    const status=document.getElementById('alumni-photo-status-v241');

    const modal=document.getElementById('alumni-crop-modal-v241');
    const closeButton=document.getElementById('alumni-crop-close-v241');
    const cancelButton=document.getElementById('alumni-crop-cancel-v241');
    const useButton=document.getElementById('alumni-crop-use-v241');
    const zoom=document.getElementById('alumni-crop-zoom-v241');
    const canvas=document.getElementById('alumni-crop-canvas-v241');
    const stage=document.querySelector('.alumni-crop-stage-v241');

    if(!input || !modal || !canvas || !stage) return;

    const ctx=canvas.getContext('2d');
    const MAX_BYTES=200*1024;
    const OUTPUT_W=354;
    const OUTPUT_H=472;

    let image=null;
    let sourceFile=null;
    let objectUrl=null;
    let baseScale=1;
    let scale=1;
    let offsetX=0;
    let offsetY=0;
    let dragging=false;
    let lastX=0;
    let lastY=0;
    let lastAcceptedFile=null;

    function showStatus(message,type){
        status.textContent=message||'';
        status.className='alumni-photo-status-v241'+(type?' '+type:'');
    }

    function openModal(){
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden','false');
        document.body.classList.add('alumni-crop-open-v241');
    }

    function closeModal(clearSelection){
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden','true');
        document.body.classList.remove('alumni-crop-open-v241');

        if(clearSelection && !lastAcceptedFile){
            input.value='';
        }
    }

    function cleanupUrl(){
        if(objectUrl){
            URL.revokeObjectURL(objectUrl);
            objectUrl=null;
        }
    }

    function clampOffsets(){
        if(!image) return;
        const drawW=image.naturalWidth*scale;
        const drawH=image.naturalHeight*scale;

        const minX=OUTPUT_W-drawW;
        const minY=OUTPUT_H-drawH;

        if(drawW<=OUTPUT_W){
            offsetX=(OUTPUT_W-drawW)/2;
        }else{
            offsetX=Math.min(0,Math.max(minX,offsetX));
        }

        if(drawH<=OUTPUT_H){
            offsetY=(OUTPUT_H-drawH)/2;
        }else{
            offsetY=Math.min(0,Math.max(minY,offsetY));
        }
    }

    function draw(){
        if(!image) return;
        ctx.clearRect(0,0,OUTPUT_W,OUTPUT_H);
        ctx.fillStyle='#18231c';
        ctx.fillRect(0,0,OUTPUT_W,OUTPUT_H);

        clampOffsets();
        ctx.drawImage(
            image,
            offsetX,
            offsetY,
            image.naturalWidth*scale,
            image.naturalHeight*scale
        );
    }

    function loadForCrop(file){
        sourceFile=file;

        if(file.size>MAX_BYTES){
            showStatus('Ukuran foto '+Math.ceil(file.size/1024)+' KB. Maksimal 200 KB.','is-error');
            input.value='';
            return;
        }

        if(!['image/jpeg','image/png'].includes(file.type)){
            showStatus('Format foto harus JPG atau PNG.','is-error');
            input.value='';
            return;
        }

        cleanupUrl();
        objectUrl=URL.createObjectURL(file);
        const img=new Image();

        img.onload=function(){
            image=img;
            baseScale=Math.max(OUTPUT_W/image.naturalWidth,OUTPUT_H/image.naturalHeight);
            scale=baseScale;
            zoom.value='1';

            const drawW=image.naturalWidth*scale;
            const drawH=image.naturalHeight*scale;
            offsetX=(OUTPUT_W-drawW)/2;
            offsetY=(OUTPUT_H-drawH)/2;

            draw();
            showStatus('');
            openModal();
        };

        img.onerror=function(){
            showStatus('Foto tidak dapat dibaca. Silakan pilih foto lain.','is-error');
            input.value='';
            cleanupUrl();
        };

        img.src=objectUrl;
    }

    function pointerPosition(event){
        const rect=canvas.getBoundingClientRect();
        return {
            x:(event.clientX-rect.left)*(OUTPUT_W/rect.width),
            y:(event.clientY-rect.top)*(OUTPUT_H/rect.height)
        };
    }

    stage.addEventListener('pointerdown',function(event){
        if(!image) return;
        dragging=true;
        stage.setPointerCapture(event.pointerId);
        const p=pointerPosition(event);
        lastX=p.x;
        lastY=p.y;
    });

    stage.addEventListener('pointermove',function(event){
        if(!dragging || !image) return;
        const p=pointerPosition(event);
        offsetX+=p.x-lastX;
        offsetY+=p.y-lastY;
        lastX=p.x;
        lastY=p.y;
        draw();
    });

    stage.addEventListener('pointerup',function(event){
        dragging=false;
        try{stage.releasePointerCapture(event.pointerId);}catch(e){}
    });

    stage.addEventListener('pointercancel',function(){
        dragging=false;
    });

    zoom.addEventListener('input',function(){
        if(!image) return;

        const oldScale=scale;
        const centerImageX=(OUTPUT_W/2-offsetX)/oldScale;
        const centerImageY=(OUTPUT_H/2-offsetY)/oldScale;

        scale=baseScale*parseFloat(zoom.value||'1');
        offsetX=OUTPUT_W/2-centerImageX*scale;
        offsetY=OUTPUT_H/2-centerImageY*scale;
        draw();
    });

    input.addEventListener('change',function(){
        const file=input.files&&input.files[0];
        if(file) loadForCrop(file);
    });

    if(changeButton){
        changeButton.addEventListener('click',function(){
            input.click();
        });
    }

    function cancelCrop(){
        if(lastAcceptedFile){
            const dt=new DataTransfer();
            dt.items.add(lastAcceptedFile);
            input.files=dt.files;
        }else{
            input.value='';
        }
        closeModal(false);
        cleanupUrl();
    }

    closeButton.addEventListener('click',cancelCrop);
    cancelButton.addEventListener('click',cancelCrop);
    modal.querySelector('.alumni-crop-modal-v241__backdrop').addEventListener('click',cancelCrop);

    document.addEventListener('keydown',function(event){
        if(event.key==='Escape'&&modal.classList.contains('is-open')){
            cancelCrop();
        }
    });

    function canvasBlob(quality){
        return new Promise(resolve=>canvas.toBlob(resolve,'image/jpeg',quality));
    }

    async function buildFinalBlob(){
        let quality=.90;
        let blob=await canvasBlob(quality);

        while(blob && blob.size>MAX_BYTES && quality>.50){
            quality-=.07;
            blob=await canvasBlob(quality);
        }

        return blob;
    }

    useButton.addEventListener('click',async function(){
        if(!image) return;

        useButton.disabled=true;
        useButton.textContent='Memproses...';

        try{
            draw();
            const blob=await buildFinalBlob();

            if(!blob){
                throw new Error('Foto belum dapat diproses.');
            }

            if(blob.size>MAX_BYTES){
                throw new Error('Hasil foto masih lebih dari 200 KB.');
            }

            const finalFile=new File(
                [blob],
                'foto-profil-alumni.jpg',
                {type:'image/jpeg',lastModified:Date.now()}
            );

            const dt=new DataTransfer();
            dt.items.add(finalFile);
            input.files=dt.files;
            lastAcceptedFile=finalFile;

            if(previewImage){
                if(previewImage.dataset.objectUrl){
                    URL.revokeObjectURL(previewImage.dataset.objectUrl);
                }
                const previewUrl=URL.createObjectURL(blob);
                previewImage.dataset.objectUrl=previewUrl;
                previewImage.src=previewUrl;
            }

            if(preview){
                preview.hidden=false;
            }
            if(picker){
                picker.style.display='none';
            }

            showStatus(
                'Foto siap digunakan · 3:4 · '+Math.ceil(blob.size/1024)+' KB',
                'is-ok'
            );
            closeModal(false);
            cleanupUrl();
        }catch(error){
            showStatus(error.message||'Foto belum dapat diproses.','is-error');
        }finally{
            useButton.disabled=false;
            useButton.textContent='Gunakan Foto';
        }
    });
})();
JS);
?>
