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
                <p class="text-muted"><b><?= Html::encode($model->application_code) ?></b> · Batch <?= (int)$model->batch->batch_number ?> · <?= Html::encode($model->batch->kabupatenKota ? $model->batch->kabupatenKota->label : '-') ?></p>
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
                <?php foreach($fields as $field): if(!$field->isFileField())continue; $rows=$documents[$field->id]??[]; ?>
                    <div style="border:1px solid #e5e8e6;border-radius:8px;padding:14px;margin-bottom:12px">
                        <b><?= Html::encode($field->label) ?><?= $field->is_required?' *':'' ?></b>
                        <?php if(!$rows): ?><p class="text-muted" style="margin:8px 0 0">Belum diunggah.</p><?php endif; ?>
                        <?php foreach($rows as $doc): ?>
                            <?php $state=['pending'=>'Belum Diperiksa','valid'=>'Valid','invalid'=>'Perlu Revisi'][$doc->verification_status]??$doc->verification_status; ?>
                            <div class="row" style="padding:10px 0;border-top:1px solid #eee;margin-top:10px">
                                <div class="col-md-7">
                                    <div><i class="fa fa-file-o"></i> <b><?= Html::encode($doc->original_name ?: 'Dokumen') ?></b></div>
                                    <small class="text-muted"><?= Html::encode($doc->mime_type ?: '-') ?> · <?= $doc->file_size ? number_format($doc->file_size/1024,0).' KB' : '-' ?></small><br>
                                    <a class="btn btn-xs btn-default" style="margin-top:6px" target="_blank" href="<?= Url::to(['/admin-applicant/document','id'=>$model->id,'documentId'=>$doc->id]) ?>"><i class="fa fa-eye"></i> Lihat Dokumen</a>
                                    <?php if($doc->verification_note): ?><div class="text-danger" style="margin-top:6px"><small><?= Html::encode($doc->verification_note) ?></small></div><?php endif; ?>
                                </div>
                                <div class="col-md-5">
                                    <?= Html::beginForm(['document-status','id'=>$model->id,'documentId'=>$doc->id],'post') ?>
                                    <?= Html::dropDownList('verification_status',$doc->verification_status,['pending'=>'Belum Diperiksa','valid'=>'Valid','invalid'=>'Perlu Revisi'],['class'=>'form-control input-sm']) ?>
                                    <?= Html::textarea('verification_note',$doc->verification_note,['class'=>'form-control input-sm','rows'=>2,'placeholder'=>'Catatan jika perlu revisi','style'=>'margin-top:6px']) ?>
                                    <button class="btn btn-primary btn-xs" style="margin-top:6px" type="submit">Simpan Pemeriksaan</button>
                                    <?= Html::endForm() ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
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
