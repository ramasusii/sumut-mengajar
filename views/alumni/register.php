<?php
use app\models\AlumniPublication;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->title='Pendaftaran Alumni';
?>
<section class="page-hero alumni-register-hero"><div class="container"><span class="section-kicker">DATABASE ALUMNI</span><h1>Ceritakan perjalananmu setelah mengabdi.</h1><p>Form ini untuk alumni Sumut Mengajar dari batch terdahulu yang belum tercatat. Data akan diverifikasi sebelum tampil di website.</p></div></section>
<section class="section soft"><div class="container alumni-register-wrap">
<?php if(Yii::$app->session->hasFlash('success')): ?><div class="alumni-success"><?= Html::encode(Yii::$app->session->getFlash('success')) ?></div><?php endif; ?>
<div class="alumni-register-card"><?php $form=ActiveForm::begin(['options'=>['enctype'=>'multipart/form-data']]); ?><h2>Identitas Pengabdian</h2><div class="alumni-form-grid"><?= $form->field($model,'nama_lengkap')->textInput(['placeholder'=>'Nama lengkap']) ?><?= $form->field($model,'whatsapp')->textInput(['placeholder'=>'081234567890','inputmode'=>'tel'])->label('Nomor WhatsApp Aktif') ?><?= $form->field($model,'batch_number')->input('number',['placeholder'=>'Contoh: 12'])->label('Alumni Batch') ?><?= $form->field($model,'batch_year')->input('number',['placeholder'=>'Contoh: 2022'])->label('Tahun Pengabdian') ?><?= $form->field($model,'location_name')->textInput(['placeholder'=>'Contoh: Samosir'])->label('Lokasi / Chapter Pengabdian') ?><div class="form-group alumni-passphoto-v24">
<label>Foto Profil</label>
<?= Html::activeFileInput($model,'photo',[
    'class'=>'form-control',
    'accept'=>'image/jpeg,image/png',
    'id'=>'alumni-photo-v24'
]) ?>
<p class="help-block">
    Pas foto resmi portrait rasio <b>3:4</b>. Minimal <b>354 × 472 px</b>,
    JPG/PNG, maksimal <b>200 KB</b>. Foto yang lolos akan diseragamkan otomatis menjadi 354 × 472 px.
</p>
<p class="alumni-photo-status-v24" id="alumni-photo-status-v24"></p>
</div></div><hr><h2>Karier Saat Ini</h2><div class="alumni-form-grid"><?= $form->field($model,'current_position')->textInput(['placeholder'=>'Contoh: Software Engineer'])->label('Jabatan') ?><?= $form->field($model,'current_institution')->textInput(['placeholder'=>'Instansi / Perusahaan'])->label('Instansi / Perusahaan') ?><?= $form->field($model,'sector')->textInput(['placeholder'=>'Pendidikan, Pemerintahan, Teknologi, dll.'])->label('Sektor') ?><?= $form->field($model,'work_city')->textInput(['placeholder'=>'Kota tempat bekerja'])->label('Kota Bekerja') ?><?= $form->field($model,'instagram')->textInput(['placeholder'=>'@username']) ?><?= $form->field($model,'linkedin')->textInput(['placeholder'=>'https://linkedin.com/in/...']) ?></div><?= $form->field($model,'bio')->textarea(['rows'=>5,'placeholder'=>'Ceritakan singkat perjalanan setelah Sumut Mengajar, dampak pengabdian, atau aktivitas saat ini.'])->label('Cerita Singkat') ?><hr><h2>Karya / Skripsi / Penelitian</h2><p class="alumni-form-note">Isi jika ada. Karya lain dapat ditambahkan oleh tim setelah verifikasi.</p><div class="alumni-form-grid"><?= $form->field($model,'publication_title')->textInput(['placeholder'=>'Judul karya / skripsi / penelitian'])->label('Judul Karya') ?><?= $form->field($model,'publication_type')->dropDownList(AlumniPublication::TYPES,['prompt'=>'Pilih jenis'])->label('Jenis Karya') ?><?= $form->field($model,'publication_year')->input('number')->label('Tahun') ?><?= $form->field($model,'publication_url')->textInput(['placeholder'=>'Tautan repository / publikasi'])->label('Tautan') ?></div><?= $form->field($model,'publication_about_service')->checkbox()->label('Karya ini membahas pengalaman atau pengabdian Sumut Mengajar') ?><hr><?= $form->field($model,'consent_public')->checkbox()->label('Saya setuju data profil, riwayat pengabdian, karier, dan karya yang saya isi dapat ditampilkan pada halaman Alumni Sumut Mengajar setelah diverifikasi. Nomor WhatsApp tidak akan ditampilkan ke publik.') ?><button class="btn-primary-gsm" type="submit">Kirim Data Alumni →</button><?php ActiveForm::end(); ?></div>
</div></section>

<?php
$this->registerCss(<<<'CSS'
.alumni-passphoto-v24 .help-block{
    margin-top:7px;
    color:#748179;
    font-size:11px;
    line-height:1.55;
}
.alumni-photo-status-v24{
    display:none;
    margin:7px 0 0;
    font-size:11px;
    font-weight:800;
}
.alumni-photo-status-v24.is-ok{
    display:block;
    color:#147443;
}
.alumni-photo-status-v24.is-error{
    display:block;
    color:#b23c32;
}
CSS);

$this->registerJs(<<<'JS'
(function(){
    const input=document.getElementById('alumni-photo-v24');
    const status=document.getElementById('alumni-photo-status-v24');
    if(!input || !status) return;

    const MAX_BYTES=200*1024;
    const MIN_W=354;
    const MIN_H=472;
    const TARGET_RATIO=3/4;
    const TOLERANCE=.015;

    function show(message,type){
        status.textContent=message;
        status.className='alumni-photo-status-v24 '+type;
    }

    input.addEventListener('change',function(){
        const file=input.files && input.files[0];
        if(!file){
            status.textContent='';
            status.className='alumni-photo-status-v24';
            return;
        }

        if(file.size>MAX_BYTES){
            show('Ukuran foto '+Math.ceil(file.size/1024)+' KB. Maksimal 200 KB.','is-error');
            input.value='';
            return;
        }

        if(!['image/jpeg','image/png'].includes(file.type)){
            show('Format foto harus JPG atau PNG.','is-error');
            input.value='';
            return;
        }

        const objectUrl=URL.createObjectURL(file);
        const img=new Image();
        img.onload=function(){
            URL.revokeObjectURL(objectUrl);
            const ratio=img.naturalWidth/img.naturalHeight;

            if(img.naturalWidth<MIN_W || img.naturalHeight<MIN_H){
                show('Resolusi minimal 354 × 472 px. Foto ini '+img.naturalWidth+' × '+img.naturalHeight+' px.','is-error');
                input.value='';
                return;
            }

            if(Math.abs(ratio-TARGET_RATIO)>TOLERANCE){
                show('Foto harus portrait rasio 3:4. Contoh ukuran 354 × 472 px.','is-error');
                input.value='';
                return;
            }

            show('Foto sesuai: '+img.naturalWidth+' × '+img.naturalHeight+' px · '+Math.ceil(file.size/1024)+' KB.','is-ok');
        };
        img.onerror=function(){
            URL.revokeObjectURL(objectUrl);
            show('Foto tidak dapat dibaca. Silakan pilih file lain.','is-error');
            input.value='';
        };
        img.src=objectUrl;
    });
})();
JS);
?>
