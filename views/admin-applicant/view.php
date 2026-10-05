<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Review Pendaftar';
$statusLabels = [
    'draft'=>'Belum Selesai','submitted'=>'Menunggu Verifikasi','revision_required'=>'Perlu Perbaikan',
    'verified'=>'Terverifikasi','administration_pass'=>'Lolos Administrasi','interview'=>'Tahap Wawancara',
    'interview_pass'=>'Lolos Wawancara','final_pass'=>'Lolos Akhir','rejected'=>'Belum Lolos',
];
$transitionMap = [
    'submitted'=>['revision_required','verified','rejected'],
    'verified'=>['revision_required','administration_pass','rejected'],
    'administration_pass'=>['interview','rejected'],
    'interview'=>['interview_pass','rejected'],
    'interview_pass'=>['final_pass','rejected'],
];
$allowedStatusOptions=[]; foreach($transitionMap[$model->status]??[] as $code){$allowedStatusOptions[$code]=$statusLabels[$code]??$code;}
$getAnswer = static function($field,$answers){$a=$answers[$field->id]??null;if(!$a)return null;if($a->answer_json){$d=json_decode($a->answer_json,true);return is_array($d)?implode(', ',$d):$a->answer_json;}return $a->answer_text;};
$profile=$model->profile;
?>

<div class="row">
    <div class="col-md-8">
        <div class="box box-success">
            <div class="box-header with-border"><h3 class="box-title">Identitas Peserta</h3><div class="box-tools"><a href="<?= Url::to(['index']) ?>" class="btn btn-default btn-xs">← Kembali</a></div></div>
            <div class="box-body">
                <h3 style="margin-top:0"><?= Html::encode($model->user->nama ?: ($profile->nama_lengkap ?? 'Peserta')) ?></h3>
                <p class="text-muted"><b><?= Html::encode($model->application_code) ?></b> · Batch <?= (int)$model->batch->batch_number ?> · <?= Html::encode($model->batch->getLocationLabel()) ?></p>
                <div class="row">
                    <div class="col-sm-6"><dl><dt>WhatsApp</dt><dd><?= Html::encode($model->user->whatsapp ?: ($profile->nomor_whatsapp ?? '-')) ?></dd><dt>Email</dt><dd><?= Html::encode($model->user->email ?: '-') ?></dd><dt>Jenis Kelamin</dt><dd><?= Html::encode($profile->jenis_kelamin ?? '-') ?></dd><dt>TTL</dt><dd><?= Html::encode(($profile->tempat_lahir ?? '-') . ($profile && $profile->tanggal_lahir ? ', '.Yii::$app->formatter->asDate($profile->tanggal_lahir) : '')) ?></dd></dl></div>
                    <div class="col-sm-6"><dl><dt>Domisili</dt><dd><?= Html::encode($profile ? $profile->getDomisiliLabel() : '-') ?></dd><dt>Jenjang</dt><dd><?= Html::encode($profile->pendidikan_terakhir ?? '-') ?></dd><dt>Instansi</dt><dd><?= Html::encode($profile->asal_instansi ?? '-') ?></dd><dt>Pekerjaan/Aktivitas</dt><dd><?= Html::encode($profile->pekerjaan ?? '-') ?></dd></dl></div>
                </div>
                <?php if($profile): ?><hr><b>Alamat Domisili</b><p><?= nl2br(Html::encode($profile->alamat_domisili ?: '-')) ?></p><?php endif; ?>
            </div>
        </div>

        <div class="box">
            <div class="box-header with-border"><h3 class="box-title">Jawaban Formulir</h3></div>
            <div class="box-body">
                <?php $hasAnswer=false; foreach($fields as $field): if($field->isFileField())continue; $hasAnswer=true; $answer=$getAnswer($field,$answers); ?>
                    <div style="padding:11px 0;border-bottom:1px solid #eee"><b><?= Html::encode($field->label) ?></b><div style="margin-top:5px;white-space:pre-line"><?= $answer!==null&&trim((string)$answer)!=='' ? nl2br(Html::encode($answer)) : '<span class="text-muted">Belum dijawab</span>' ?></div></div>
                <?php endforeach; if(!$hasAnswer): ?><p class="text-muted">Belum ada pertanyaan seleksi.</p><?php endif; ?>
            </div>
        </div>

        <div class="box box-warning" id="dokumen">
            <div class="box-header with-border"><h3 class="box-title">Dokumen & Bukti Upload</h3><div class="box-tools"><span class="label label-default"><?= (int)$uploadedRequiredCount ?>/<?= (int)$requiredDocumentCount ?> terunggah</span> <span class="label label-success"><?= (int)$validRequiredCount ?> valid</span></div></div>
            <div class="box-body">
                <?php foreach($fields as $field): if(!$field->isFileField())continue; $rows=$documents[$field->id]??[]; $isMulti=$field->field_type==='multi_file'; ?>
                    <?php
                        $validation = json_decode((string)$field->validation_json, true) ?: [];
                        $maxFiles = $isMulti ? max(1, (int)($validation['maxFiles'] ?? $validation['max_files'] ?? 5)) : 1;
                    ?>
                    <div class="verify-field-card">
                        <div class="verify-field-head">
                            <div>
                                <b><?= Html::encode($field->label) ?><?= $field->is_required?' *':'' ?></b>
                                <?php if($isMulti): ?><small>Semua bukti tampil di bawah dan diperiksa satu per satu.</small><?php endif; ?>
                            </div>
                            <?php if($isMulti): ?><span class="label label-default"><?= count($rows) ?>/<?= (int)$maxFiles ?> bukti</span><?php endif; ?>
                        </div>

                        <?php if(!$rows): ?><p class="text-muted" style="margin:10px 0 0">Belum diunggah.</p><?php endif; ?>

                        <div class="<?= $isMulti ? 'verify-proof-grid' : '' ?>">
                        <?php foreach($rows as $docIndex => $doc): ?>
                            <?php
                                $state=['pending'=>'Belum Diperiksa','valid'=>'Valid','invalid'=>'Perlu Revisi'][$doc->verification_status]??$doc->verification_status;
                                $previewUrl=Url::to(['/admin-applicant/document','id'=>$model->id,'documentId'=>$doc->id]);
                                $mime=(string)($doc->mime_type ?: '');
                                $isImage=str_starts_with($mime,'image/') || preg_match('/\.(jpg|jpeg|png|webp)$/i',(string)$doc->original_name);
                                $isPdf=$mime==='application/pdf' || preg_match('/\.pdf$/i',(string)$doc->original_name);
                            ?>
                            <div class="verify-proof-item">
                                <?php if($isMulti): ?><div class="verify-proof-number">Bukti <?= (int)$docIndex + 1 ?></div><?php endif; ?>
                                <div class="verify-proof-file"><i class="fa fa-file-o"></i> <b><?= Html::encode($doc->original_name ?: 'Dokumen') ?></b></div>
                                <small class="text-muted"><?= Html::encode($doc->mime_type ?: '-') ?> · <?= $doc->file_size ? number_format($doc->file_size/1024,0).' KB' : '-' ?> · <?= Html::encode($state) ?></small>

                                <div class="verify-proof-actions">
                                    <button
                                        type="button"
                                        class="btn btn-xs btn-default js-document-preview"
                                        data-url="<?= Html::encode($previewUrl) ?>"
                                        data-name="<?= Html::encode($doc->original_name ?: 'Dokumen') ?>"
                                        data-kind="<?= $isImage ? 'image' : ($isPdf ? 'pdf' : 'other') ?>"
                                    ><i class="fa fa-eye"></i> Lihat Dokumen</button>
                                </div>

                                <?php if($doc->verification_note): ?><div class="text-danger verify-existing-note"><small><?= Html::encode($doc->verification_note) ?></small></div><?php endif; ?>

                                <div class="verify-proof-form">
                                    <?= Html::beginForm(['document-status','id'=>$model->id,'documentId'=>$doc->id],'post') ?>
                                    <?= Html::dropDownList('verification_status',$doc->verification_status,['pending'=>'Belum Diperiksa','valid'=>'Valid','invalid'=>'Perlu Revisi'],['class'=>'form-control input-sm']) ?>
                                    <?= Html::textarea('verification_note',$doc->verification_note,['class'=>'form-control input-sm','rows'=>2,'placeholder'=>'Catatan jika perlu revisi','style'=>'margin-top:6px']) ?>
                                    <button class="btn btn-primary btn-xs" style="margin-top:6px" type="submit">Simpan Pemeriksaan</button>
                                    <?= Html::endForm() ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Keputusan Seleksi</h3></div>
            <div class="box-body">
                <p>Status saat ini: <b><?= Html::encode($statusLabels[$model->status] ?? $model->status) ?></b></p>
                <?php if($model->status==='draft'): ?>
                    <div class="alert alert-warning">Peserta belum mengirim formulir.</div>
                <?php elseif($model->status==='revision_required'): ?>
                    <div class="alert alert-warning">Menunggu peserta mengirim ulang perbaikan berkas.</div>
                <?php elseif(!$allowedStatusOptions): ?>
                    <div class="alert alert-info">Proses seleksi peserta ini sudah selesai.</div>
                <?php else: ?>
                    <?= Html::beginForm(['status','id'=>$model->id],'post') ?>
                    <div class="form-group"><label>Keputusan Berikutnya</label><?= Html::dropDownList('status','',$allowedStatusOptions,['class'=>'form-control','prompt'=>'Pilih keputusan']) ?></div>
                    <div class="form-group"><label>Catatan Verifikator</label><?= Html::textarea('status_note','',['class'=>'form-control','rows'=>4,'placeholder'=>'Wajib untuk Perlu Perbaikan / Belum Lolos']) ?></div>
                    <button class="btn btn-success btn-block" type="submit"><i class="fa fa-check"></i> Simpan Keputusan</button>
                    <?= Html::endForm() ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="box" id="catatan">
            <div class="box-header with-border"><h3 class="box-title">Catatan Internal Tim</h3></div>
            <div class="box-body">
                <?= Html::beginForm(['note','id'=>$model->id],'post') ?><?= Html::textarea('note','',['class'=>'form-control','rows'=>3,'placeholder'=>'Catatan hanya untuk petugas']) ?><button class="btn btn-default btn-block" style="margin-top:7px" type="submit">Tambah Catatan</button><?= Html::endForm() ?>
                <hr>
                <?php if(!$model->notes): ?><p class="text-muted">Belum ada catatan.</p><?php endif; ?>
                <?php foreach($model->notes as $note): ?><div style="padding:8px 0;border-bottom:1px solid #eee"><b><?= Html::encode($note->user ? ($note->user->nama ?: $note->user->username) : 'Tim') ?></b><br><small class="text-muted"><?= Yii::$app->formatter->asDatetime($note->created_at) ?></small><p style="margin:5px 0 0"><?= nl2br(Html::encode($note->note)) ?></p></div><?php endforeach; ?>
            </div>
        </div>
    </div>
</div>


<div class="gsm-document-modal" id="gsmDocumentModal" aria-hidden="true">
    <div class="gsm-document-modal__backdrop" data-doc-close></div>
    <div class="gsm-document-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="gsmDocumentModalTitle">
        <div class="gsm-document-modal__head">
            <div><small>PRATINJAU DOKUMEN</small><h4 id="gsmDocumentModalTitle">Dokumen Peserta</h4></div>
            <button type="button" class="gsm-document-modal__close" data-doc-close aria-label="Tutup">×</button>
        </div>
        <div class="gsm-document-modal__body">
            <img id="gsmDocumentModalImage" alt="Pratinjau dokumen peserta" hidden>
            <iframe id="gsmDocumentModalFrame" title="Pratinjau dokumen peserta" hidden></iframe>
            <div id="gsmDocumentModalFallback" class="gsm-document-modal__fallback" hidden>
                File ini tidak dapat ditampilkan sebagai pratinjau.
                <a id="gsmDocumentModalLink" target="_blank" rel="noopener">Buka dokumen</a>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerCss(<<<'CSS'
.verify-field-card{border:1px solid #e5e8e6;border-radius:10px;padding:14px;margin-bottom:12px;background:#fff}
.verify-field-head{display:flex;justify-content:space-between;gap:12px;align-items:flex-start}.verify-field-head small{display:block;color:#849087;margin-top:4px;font-weight:400}
.verify-proof-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:10px}
.verify-proof-item{border:1px solid #edf0ee;border-radius:9px;padding:12px;background:#fcfdfc}.verify-proof-number{display:inline-block;margin-bottom:8px;padding:3px 7px;border-radius:999px;background:#e9f6ee;color:#147343;font-size:10px;font-weight:800}
.verify-proof-file{overflow-wrap:anywhere}.verify-proof-actions{margin-top:8px}.verify-proof-form{margin-top:10px;padding-top:10px;border-top:1px solid #edf0ee}.verify-existing-note{margin-top:7px}
.gsm-document-modal{position:fixed;inset:0;z-index:99999;display:none;align-items:center;justify-content:center;padding:24px}.gsm-document-modal.is-open{display:flex}.gsm-document-modal__backdrop{position:absolute;inset:0;background:rgba(9,25,17,.72);backdrop-filter:blur(3px)}
.gsm-document-modal__dialog{position:relative;width:min(980px,96vw);height:min(820px,90vh);display:flex;flex-direction:column;background:#fff;border-radius:16px;box-shadow:0 30px 90px rgba(0,0,0,.35);overflow:hidden}.gsm-document-modal__head{display:flex;align-items:center;justify-content:space-between;gap:18px;padding:15px 18px;border-bottom:1px solid #e9eeeb}.gsm-document-modal__head small{color:#16824a;font-size:9px;font-weight:800;letter-spacing:.8px}.gsm-document-modal__head h4{margin:2px 0 0;font-size:15px}.gsm-document-modal__close{width:36px;height:36px;border:0;border-radius:50%;background:#eff4f1;color:#26362d;font-size:24px;line-height:1;cursor:pointer}
.gsm-document-modal__body{flex:1;min-height:0;display:flex;align-items:center;justify-content:center;padding:14px;background:#f5f7f5;overflow:auto}.gsm-document-modal__body img{display:block;max-width:100%;max-height:100%;object-fit:contain;border-radius:8px;background:#fff}.gsm-document-modal__body iframe{width:100%;height:100%;min-height:620px;border:0;border-radius:8px;background:#fff}.gsm-document-modal__fallback{text-align:center;color:#65736a}.gsm-document-modal__fallback a{display:inline-block;margin-left:7px;font-weight:700;color:#147343}
body.gsm-modal-open{overflow:hidden}
@media(max-width:767px){.verify-proof-grid{grid-template-columns:1fr}.gsm-document-modal{padding:10px}.gsm-document-modal__dialog{width:100%;height:92vh}.gsm-document-modal__body iframe{min-height:520px}}
CSS);

$this->registerJs(<<<'JS'
(function(){
    const modal=document.getElementById('gsmDocumentModal');
    if(!modal) return;
    const title=document.getElementById('gsmDocumentModalTitle');
    const image=document.getElementById('gsmDocumentModalImage');
    const frame=document.getElementById('gsmDocumentModalFrame');
    const fallback=document.getElementById('gsmDocumentModalFallback');
    const link=document.getElementById('gsmDocumentModalLink');

    function reset(){
        image.hidden=true; image.removeAttribute('src');
        frame.hidden=true; frame.removeAttribute('src');
        fallback.hidden=true; link.removeAttribute('href');
    }
    function closeModal(){
        modal.classList.remove('is-open'); modal.setAttribute('aria-hidden','true');
        document.body.classList.remove('gsm-modal-open'); reset();
    }
    document.addEventListener('click',function(e){
        const btn=e.target.closest('.js-document-preview');
        if(btn){
            const url=btn.dataset.url, kind=btn.dataset.kind, name=btn.dataset.name || 'Dokumen Peserta';
            reset(); title.textContent=name;
            if(kind==='image'){ image.src=url; image.hidden=false; }
            else if(kind==='pdf'){ frame.src=url; frame.hidden=false; }
            else { link.href=url; fallback.hidden=false; }
            modal.classList.add('is-open'); modal.setAttribute('aria-hidden','false'); document.body.classList.add('gsm-modal-open');
            return;
        }
        if(e.target.closest('[data-doc-close]')) closeModal();
    });
    document.addEventListener('keydown',function(e){ if(e.key==='Escape' && modal.classList.contains('is-open')) closeModal(); });
})();
JS);
?>
