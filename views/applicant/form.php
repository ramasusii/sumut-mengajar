<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $isRevision ? 'Perbaiki Berkas' : 'Formulir Pendaftaran';

$nonFileFields = array_values(array_filter($fields, static fn($field) => !$field->isFileField()));
$fileFields = array_values(array_filter($fields, static fn($field) => $field->isFileField()));
$stepLabels = [1 => 'Data Diri', 2 => 'Pertanyaan', 3 => 'Dokumen', 4 => 'Tinjau & Kirim'];
$maxUploadBytes = 200 * 1024;
$maxUploadLabel = '200 KB';

$answerValue = static function ($field, $answers) {
    $answer = $answers[$field->id] ?? null;
    if (!$answer) return null;
    if ($answer->answer_json) {
        $decoded = json_decode($answer->answer_json, true);
        return is_array($decoded) ? $decoded : [];
    }
    return $answer->answer_text;
};

$fieldLimits = static function ($field) {
    $config = json_decode((string)$field->validation_json, true);
    $config = is_array($config) ? $config : [];
    $min = max(1, (int)($config['min_files'] ?? 1));
    $max = $field->field_type === 'multi_file' ? max($min, (int)($config['max_files'] ?? 5)) : 1;
    return [$min, $max];
};
?>

<div class="application-form-page launch-form-v3">
    <div class="application-form-head">
        <div>
            <a href="<?= Url::to(['/applicant/application', 'id' => $application->id]) ?>">← Kembali ke Tracking</a>
            <span class="application-form-code"><?= Html::encode($application->application_code) ?></span>
            <h1><?= Html::encode($application->batch->title) ?></h1>
            <p><?= $isRevision ? 'Perbaiki dokumen yang ditandai panitia, lalu kirim ulang untuk diperiksa.' : 'Lengkapi formulir secara bertahap. Data tersimpan setiap kali kamu menekan tombol lanjut.' ?></p>
        </div>
    </div>

    <div class="application-form-progress">
        <?php foreach ($stepLabels as $number => $label): ?>
            <?php $class = $number < $step ? 'done' : ($number === $step ? 'active' : ''); ?>
            <div class="item <?= $class ?>">
                <span class="num"><?= $number < $step ? '✓' : $number ?></span>
                <div><small>LANGKAH <?= $number ?></small><strong><?= Html::encode($label) ?></strong></div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($errors): ?>
        <div class="application-form-errors">
            <strong>Periksa kembali data berikut:</strong>
            <ul><?php foreach ($errors as $error): ?><li><?= Html::encode($error) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <?php if ($step === 1 && !$isRevision): ?>
        <section class="application-form-card">
            <div class="application-form-card-head"><span>LANGKAH 1 DARI 4</span><h2>Data Diri</h2><p>Gunakan data aktif dan benar agar panitia mudah menghubungimu.</p></div>
            <?= Html::beginForm(['/applicant/form', 'id' => $application->id, 'step' => 1], 'post') ?>
            <div class="application-form-grid">
                <div class="application-form-group"><label>Nama Lengkap <em>*</em></label><?= Html::textInput('profile[nama_lengkap]', $profile->nama_lengkap, ['required'=>true]) ?></div>
                <div class="application-form-group"><label>Jenis Kelamin <em>*</em></label><?= Html::dropDownList('profile[jenis_kelamin]', $profile->jenis_kelamin, ['Laki-laki'=>'Laki-laki','Perempuan'=>'Perempuan'], ['prompt'=>'Pilih jenis kelamin','required'=>true]) ?></div>
                <div class="application-form-group"><label>Tempat Lahir <em>*</em></label><?= Html::textInput('profile[tempat_lahir]', $profile->tempat_lahir, ['required'=>true]) ?></div>
                <div class="application-form-group"><label>Tanggal Lahir <em>*</em></label><?= Html::input('date', 'profile[tanggal_lahir]', $profile->tanggal_lahir, ['required'=>true]) ?><div class="application-form-help">Usia peserta 18–28 tahun pada saat pendaftaran.</div></div>
                <div class="application-form-group"><label>Agama <em>*</em></label><?= Html::dropDownList('profile[agama]', $profile->agama, ['Islam'=>'Islam','Kristen Protestan'=>'Kristen Protestan','Katolik'=>'Katolik','Hindu'=>'Hindu','Buddha'=>'Buddha','Konghucu'=>'Konghucu','Lainnya'=>'Lainnya'], ['prompt'=>'Pilih agama','required'=>true]) ?></div>
                <div class="application-form-group"><label>Nomor WhatsApp <em>*</em></label><?= Html::textInput('profile[nomor_whatsapp]', $profile->nomor_whatsapp, ['required'=>true,'placeholder'=>'081234567890','inputmode'=>'tel']) ?><div class="application-form-help">Nomor ini juga digunakan untuk akun peserta.</div></div>
                <div class="application-form-group"><label>Provinsi Domisili <em>*</em></label><?= Html::textInput('profile[provinsi_domisili]', $profile->provinsi_domisili, ['required'=>true,'placeholder'=>'Contoh: Sumatera Utara']) ?></div>
                <div class="application-form-group"><label>Kabupaten/Kota Domisili <em>*</em></label><?= Html::textInput('profile[kabupaten_kota_domisili]', $profile->kabupaten_kota_domisili, ['required'=>true,'placeholder'=>'Contoh: Kota Medan']) ?></div>
                <div class="application-form-group"><label>Asal Instansi <em>*</em></label><?= Html::textInput('profile[asal_instansi]', $profile->asal_instansi, ['required'=>true,'placeholder'=>'Kampus / Instansi']) ?></div>
                <div class="application-form-group"><label>Jenjang Pendidikan <em>*</em></label><?= Html::dropDownList('profile[pendidikan_terakhir]', $profile->pendidikan_terakhir, ['S1'=>'S1','D4'=>'D4'], ['prompt'=>'Pilih jenjang','required'=>true]) ?><div class="application-form-help">Rekrutmen ini diperuntukkan bagi jenjang S1 atau D4.</div></div>
                <div class="application-form-group"><label>Pekerjaan / Aktivitas Saat Ini</label><?= Html::textInput('profile[pekerjaan]', $profile->pekerjaan, ['placeholder'=>'Mahasiswa, karyawan, freelancer, dll.']) ?></div>
                <div class="application-form-group"><label>Instagram</label><?= Html::textInput('profile[instagram]', $profile->instagram, ['placeholder'=>'@username']) ?></div>
                <div class="application-form-group"><label>TikTok</label><?= Html::textInput('profile[tiktok]', $profile->tiktok, ['placeholder'=>'@username']) ?></div>
                <div class="application-form-group full"><label>Alamat Domisili <em>*</em></label><?= Html::textarea('profile[alamat_domisili]', $profile->alamat_domisili, ['required'=>true,'rows'=>3,'placeholder'=>'Alamat lengkap tempat tinggal saat ini']) ?></div>
            </div>
            <div class="application-form-actions"><span></span><button type="submit">Simpan & Lanjut →</button></div>
            <?= Html::endForm() ?>
        </section>

    <?php elseif ($step === 2 && !$isRevision): ?>
        <section class="application-form-card">
            <div class="application-form-card-head"><span>LANGKAH 2 DARI 4</span><h2>Pertanyaan Seleksi</h2><p>Jawab dengan jujur dan gunakan pengalamanmu sendiri.</p></div>
            <?php if (!$nonFileFields): ?>
                <div class="application-form-errors"><strong>Pertanyaan seleksi belum tersedia.</strong><ul><li>Silakan hubungi panitia Sumut Mengajar.</li></ul></div>
            <?php else: ?>
                <?= Html::beginForm(['/applicant/form', 'id'=>$application->id, 'step'=>2], 'post') ?>
                <div class="application-form-grid">
                    <?php foreach ($nonFileFields as $field): ?>
                        <?php $value=$answerValue($field,$answers); $full=in_array($field->field_type,['textarea','radio','checkbox'],true)?'full':''; ?>
                        <div class="application-form-group <?= $full ?>">
                            <label><?= Html::encode($field->label) ?> <?= (int)$field->is_required===1?'<em>*</em>':'' ?></label>
                            <?php if ($field->field_type==='textarea'): ?>
                                <?= Html::textarea('answers['.$field->id.']', is_array($value)?'':$value, ['rows'=>5,'placeholder'=>$field->placeholder,'required'=>(int)$field->is_required===1]) ?>
                            <?php elseif ($field->field_type==='radio'): ?>
                                <div class="application-form-options"><?php foreach($field->options as $option): ?><label class="application-form-option"><?= Html::radio('answers['.$field->id.']', $value===$option->value, ['value'=>$option->value,'required'=>(int)$field->is_required===1]) ?> <?= Html::encode($option->label) ?></label><?php endforeach; ?></div>
                            <?php elseif ($field->field_type==='checkbox'): ?>
                                <div class="application-form-options"><?php foreach($field->options as $option): ?><?php $checked=is_array($value)&&in_array($option->value,$value,true); ?><label class="application-form-option"><?= Html::checkbox('answers['.$field->id.'][]',$checked,['value'=>$option->value]) ?> <?= Html::encode($option->label) ?></label><?php endforeach; ?></div>
                            <?php elseif ($field->field_type==='select'): ?>
                                <?= Html::dropDownList('answers['.$field->id.']',$value,ArrayHelper::map($field->options,'value','label'),['prompt'=>'Pilih jawaban','required'=>(int)$field->is_required===1]) ?>
                            <?php elseif ($field->field_type==='date'): ?>
                                <?= Html::input('date','answers['.$field->id.']',is_array($value)?'':$value,['required'=>(int)$field->is_required===1]) ?>
                            <?php else: ?>
                                <?= Html::textInput('answers['.$field->id.']',is_array($value)?'':$value,['placeholder'=>$field->placeholder,'required'=>(int)$field->is_required===1]) ?>
                            <?php endif; ?>
                            <?php if ($field->help_text): ?><div class="application-form-help"><?= Html::encode($field->help_text) ?></div><?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="application-form-actions"><a class="back" href="<?= Url::to(['/applicant/form','id'=>$application->id,'step'=>1]) ?>">← Kembali</a><button type="submit">Simpan & Lanjut →</button></div>
                <?= Html::endForm() ?>
            <?php endif; ?>
        </section>

    <?php elseif ($step === 3): ?>
        <section class="application-form-card">
            <div class="application-form-card-head"><span>LANGKAH 3 DARI 4</span><h2><?= $isRevision ? 'Perbaiki Dokumen' : 'Dokumen & Bukti Persyaratan' ?></h2><p>File yang diterima: JPG, PNG, atau PDF. Maksimal <?= Html::encode($maxUploadLabel) ?> per file.</p></div>
            <?php if (!$fileFields): ?>
                <div class="application-form-errors"><strong>Persyaratan dokumen belum tersedia.</strong><ul><li>Silakan hubungi panitia Sumut Mengajar.</li></ul></div>
            <?php else: ?>
                <?= Html::beginForm(['/applicant/form','id'=>$application->id,'step'=>3],'post',['enctype'=>'multipart/form-data']) ?>
                <?php foreach ($fileFields as $field): ?>
                    <?php [$minFiles,$maxFiles]=$fieldLimits($field); $existing=$documents[$field->id]??[]; $invalid=array_filter($existing,static fn($doc)=>$doc->verification_status==='invalid'); ?>
                    <div class="application-form-document <?= $invalid?'needs-revision':'' ?>">
                        <div>
                            <h3><?= Html::encode($field->label) ?> <?= (int)$field->is_required?'<span style="color:#e06e43">*</span>':'' ?></h3>
                            <?php if ($field->help_text && !preg_match('/\\b(?:KB|MB|GB)\\b/i', (string)$field->help_text)): ?>
                                <p><?= Html::encode($field->help_text) ?></p>
                            <?php endif; ?>
                            <p class="application-file-limit"><b>JPG, PNG, atau PDF • maksimal <?= Html::encode($maxUploadLabel) ?> per file.</b></p>
                            <?php if ($field->field_type==='multi_file'): ?><p><b>Minimal <?= $minFiles ?> file, maksimal <?= $maxFiles ?> file.</b></p><?php endif; ?>
                            <?php foreach ($existing as $doc): ?>
                                <span class="existing <?= $doc->verification_status==='invalid'?'invalid':'' ?>"> <?= $doc->verification_status==='invalid'?'!':'✓' ?> <?= Html::encode($doc->original_name ?: 'dokumen') ?><?= $doc->verification_note ? ' — '.Html::encode($doc->verification_note) : '' ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php if ($field->field_type==='multi_file'): ?>
                            <div class="application-multifile-grid">
                                <?php for ($slot = 1; $slot <= $maxFiles; $slot++): ?>
                                    <div class="application-multifile-slot">
                                        <div class="application-multifile-slot__label">
                                            Bukti grup <?= $slot ?>
                                            <?php if ($slot <= $minFiles): ?>
                                                <span class="application-multifile-required">*</span>
                                            <?php else: ?>
                                                <span class="application-multifile-optional">opsional</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="application-file-control">
                                            <input
                                                class="application-file-input"
                                                type="file"
                                                name="upload_<?= (int)$field->id ?>[]"
                                                accept=".jpg,.jpeg,.png,.pdf"
                                                data-max-size="<?= (int)$maxUploadBytes ?>"
                                                data-max-files="1"
                                            >
                                            <div class="application-file-status" aria-live="polite">Belum ada file dipilih.</div>
                                            <div class="application-file-error" aria-live="assertive" hidden></div>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        <?php else: ?>
                            <div class="application-file-control">
                                <input class="application-file-input" type="file" name="upload_<?= (int)$field->id ?>" accept=".jpg,.jpeg,.png,.pdf" data-max-size="<?= (int)$maxUploadBytes ?>" data-max-files="1">
                                <div class="application-file-status" aria-live="polite">Belum ada file dipilih.</div>
                                <div class="application-file-error" aria-live="assertive" hidden></div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <div class="application-form-actions">
                    <?php if (!$isRevision): ?><a class="back" href="<?= Url::to(['/applicant/form','id'=>$application->id,'step'=>2]) ?>">← Kembali</a><?php else: ?><span></span><?php endif; ?>
                    <button type="submit">Simpan & Lanjut →</button>
                </div>
                <?= Html::endForm() ?>
            <?php endif; ?>
        </section>

    <?php else: ?>
        <section class="application-form-card">
            <div class="application-form-card-head"><span>LANGKAH 4 DARI 4</span><h2><?= $isRevision ? 'Kirim Ulang Perbaikan' : 'Tinjau Pendaftaran' ?></h2><p>Pastikan data dan berkas sudah benar sebelum dikirim ke panitia.</p></div>
            <div class="application-review-grid">
                <div class="application-review-card"><h3>Data Diri</h3><div class="application-review-row"><span>Nama</span><b><?= Html::encode($profile->nama_lengkap ?: '-') ?></b></div><div class="application-review-row"><span>WhatsApp</span><b><?= Html::encode($profile->nomor_whatsapp ?: '-') ?></b></div><div class="application-review-row"><span>Domisili</span><b><?= Html::encode($profile->getDomisiliLabel()) ?></b></div><div class="application-review-row"><span>Jenjang</span><b><?= Html::encode($profile->pendidikan_terakhir ?: '-') ?></b></div></div>
                <div class="application-review-card"><h3>Kelengkapan</h3><div class="application-review-row"><span>Pertanyaan</span><b><?= count($answers) ?> / <?= count($nonFileFields) ?></b></div><div class="application-review-row"><span>Jenis Dokumen</span><b><?= count(array_filter($documents)) ?> / <?= count($fileFields) ?></b></div><div class="application-review-row"><span>Status</span><b><?= $isRevision?'Perbaikan Siap Dikirim':'Belum Dikirim' ?></b></div></div>
            </div>
            <div class="application-submit-box"><div><span><?= $isRevision?'KIRIM ULANG BERKAS':'KIRIM PENDAFTARAN' ?></span><h3><?= $isRevision?'Sudah selesai memperbaiki berkas?':'Sudah yakin semua data benar?' ?></h3><p>Setelah dikirim, panitia akan melakukan pemeriksaan.</p></div><?= Html::beginForm(['/applicant/submit','id'=>$application->id],'post') ?><button type="submit" onclick="return confirm('Kirim sekarang? Pastikan semua data dan dokumen sudah benar.')"><?= $isRevision?'Kirim Ulang →':'Kirim Pendaftaran →' ?></button><?= Html::endForm() ?></div>
            <div class="application-form-actions"><a class="back" href="<?= Url::to(['/applicant/form','id'=>$application->id,'step'=>3]) ?>">← Kembali ke Dokumen</a><span></span></div>
        </section>
    <?php endif; ?>
</div>

<?php
$this->registerCss(<<<'CSS'
/* FORM V13 — compact premium radio/checkbox */
.launch-form-v3 .application-form-options{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
    gap:10px;
}
.launch-form-v3 .application-form-option{
    display:flex;
    align-items:center;
    gap:12px;
    min-height:64px;
    margin:0;
    padding:13px 15px;
    border:1px solid #dfe7e1;
    border-radius:14px;
    background:#fbfcfb;
    color:#1f2d25;
    line-height:1.45;
    cursor:pointer;
    transition:border-color .18s ease,background .18s ease,box-shadow .18s ease,transform .18s ease;
}
.launch-form-v3 .application-form-option:hover{
    border-color:#b8d4c1;
    background:#f7fbf8;
    transform:translateY(-1px);
}
.launch-form-v3 .application-form-option input[type="radio"],
.launch-form-v3 .application-form-option input[type="checkbox"]{
    -webkit-appearance:none !important;
    appearance:none !important;
    width:22px !important;
    min-width:22px !important;
    max-width:22px !important;
    height:22px !important;
    min-height:22px !important;
    margin:0 !important;
    padding:0 !important;
    border:2px solid #c9d7ce !important;
    background:#fff !important;
    box-shadow:none !important;
    outline:none;
    display:grid;
    place-content:center;
    flex:0 0 22px;
    cursor:pointer;
}
.launch-form-v3 .application-form-option input[type="radio"]{border-radius:50% !important}
.launch-form-v3 .application-form-option input[type="checkbox"]{border-radius:6px !important}
.launch-form-v3 .application-form-option input[type="radio"]:checked{
    border:6px solid #0f7a46 !important;
    background:#fff !important;
}
.launch-form-v3 .application-form-option input[type="checkbox"]:checked{
    border-color:#0f7a46 !important;
    background:#0f7a46 !important;
}
.launch-form-v3 .application-form-option input[type="checkbox"]:checked::after{
    content:"✓";
    color:#fff;
    font-size:14px;
    line-height:1;
    font-weight:900;
}
.launch-form-v3 .application-form-option:has(input:checked){
    border-color:#99c8aa;
    background:#eff8f2;
    box-shadow:0 0 0 3px rgba(15,122,70,.07);
}
.launch-form-v3 .application-file-limit{
    color:#176c3f !important;
}
.launch-form-v3 .application-file-control{
    display:flex;
    flex-direction:column;
    gap:7px;
}
.launch-form-v3 .application-file-input{
    width:100%;
    max-width:100%;
    min-height:44px;
    padding:7px 8px;
    border:1px dashed #b8cbbf;
    border-radius:11px;
    background:#fff;
    font-size:10px;
}
.launch-form-v3 .application-file-input::file-selector-button{
    border:0;
    border-radius:9px;
    padding:8px 11px;
    margin-right:9px;
    background:#eaf5ee;
    color:#0e623a;
    font-size:10px;
    font-weight:900;
    cursor:pointer;
}
.launch-form-v3 .application-file-status{
    font-size:9px;
    color:#7b8981;
    line-height:1.45;
}
.launch-form-v3 .application-file-error{
    padding:8px 10px;
    border:1px solid #efc8bf;
    border-radius:9px;
    background:#fff2ef;
    color:#9a4538;
    font-size:9px;
    font-weight:800;
    line-height:1.45;
}
.launch-form-v3 .application-multifile-grid{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:10px;
    width:100%;
}
.launch-form-v3 .application-multifile-slot{
    padding:11px;
    border:1px solid #e1e9e3;
    border-radius:12px;
    background:#fcfdfc;
}
.launch-form-v3 .application-multifile-slot__label{
    margin-bottom:7px;
    color:#24372c;
    font-size:10px;
    font-weight:900;
}
.launch-form-v3 .application-multifile-required{
    color:#d4563f;
}
.launch-form-v3 .application-multifile-optional{
    margin-left:5px;
    color:#8a978f;
    font-size:8px;
    font-weight:700;
}
@media(max-width:720px){
    .launch-form-v3 .application-form-options{grid-template-columns:1fr}
    .launch-form-v3 .application-form-option{min-height:58px}
    .launch-form-v3 .application-multifile-grid{grid-template-columns:1fr}
}
CSS);

$this->registerJs(<<<'JS'
(function () {
    const formatKb = bytes => Math.ceil(bytes / 1024) + ' KB';

    function validateFileInput(input) {
        const maxSize = Number(input.dataset.maxSize || 204800);
        const maxFiles = Number(input.dataset.maxFiles || 1);
        const wrap = input.closest('.application-file-control');
        const status = wrap ? wrap.querySelector('.application-file-status') : null;
        const error = wrap ? wrap.querySelector('.application-file-error') : null;
        const files = Array.from(input.files || []);

        if (error) {
            error.hidden = true;
            error.textContent = '';
        }

        if (!files.length) {
            if (status) status.textContent = 'Belum ada file dipilih.';
            return true;
        }

        if (files.length > maxFiles) {
            if (error) {
                error.hidden = false;
                error.textContent = 'Maksimal ' + maxFiles + ' file untuk bagian ini.';
            }
            input.value = '';
            if (status) status.textContent = 'Belum ada file dipilih.';
            return false;
        }

        const oversized = files.find(file => file.size > maxSize);
        if (oversized) {
            if (error) {
                error.hidden = false;
                error.textContent = 'File "' + oversized.name + '" berukuran ' + formatKb(oversized.size) +
                    '. Maksimal 200 KB per file. Silakan kompres atau pilih file lain.';
            }
            input.value = '';
            if (status) status.textContent = 'File ditolak karena melebihi 200 KB.';
            return false;
        }

        const allowed = ['image/jpeg', 'image/png', 'application/pdf'];
        const invalidType = files.find(file => file.type && !allowed.includes(file.type));
        if (invalidType) {
            if (error) {
                error.hidden = false;
                error.textContent = 'Format "' + invalidType.name + '" tidak didukung. Gunakan JPG, PNG, atau PDF.';
            }
            input.value = '';
            if (status) status.textContent = 'Belum ada file dipilih.';
            return false;
        }

        if (status) {
            status.textContent = files.length === 1
                ? files[0].name + ' • ' + formatKb(files[0].size)
                : files.length + ' file dipilih • semua file maksimal 200 KB';
        }
        return true;
    }

    document.querySelectorAll('.application-file-input').forEach(input => {
        input.addEventListener('change', () => validateFileInput(input));
    });

    document.querySelectorAll('.launch-form-v3 form[enctype="multipart/form-data"]').forEach(form => {
        form.addEventListener('submit', function (event) {
            let valid = true;
            form.querySelectorAll('.application-file-input').forEach(input => {
                if (!validateFileInput(input)) valid = false;
            });
            if (!valid) {
                event.preventDefault();
                const firstError = form.querySelector('.application-file-error:not([hidden])');
                if (firstError) firstError.scrollIntoView({behavior:'smooth', block:'center'});
            }
        });
    });
})();
JS);
?>
