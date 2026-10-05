<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Review Pendaftar';
$statusLabels = [
    'draft' => 'Belum Selesai',
    'submitted' => 'Menunggu Verifikasi',
    'revision_required' => 'Perlu Perbaikan',
    'verified' => 'Terverifikasi',
    'administration_pass' => 'Lolos Administrasi',
    'interview' => 'Tahap Wawancara',
    'interview_pass' => 'Lolos Wawancara',
    'final_pass' => 'Lolos Akhir',
    'rejected' => 'Belum Lolos',
];
$statusTone = [
    'draft' => 'slate',
    'submitted' => 'amber',
    'revision_required' => 'orange',
    'verified' => 'blue',
    'administration_pass' => 'green',
    'interview' => 'purple',
    'interview_pass' => 'teal',
    'final_pass' => 'green',
    'rejected' => 'red',
];
$transitionMap = [
    'submitted' => ['revision_required', 'verified', 'rejected'],
    'verified' => ['revision_required', 'administration_pass', 'rejected'],
    'administration_pass' => ['interview', 'rejected'],
    'interview' => ['interview_pass', 'rejected'],
    'interview_pass' => ['final_pass', 'rejected'],
];
$allowedStatusOptions = [];
foreach ($transitionMap[$model->status] ?? [] as $code) {
    $allowedStatusOptions[$code] = $statusLabels[$code] ?? $code;
}
$getAnswer = static function ($field, $answers) {
    $a = $answers[$field->id] ?? null;
    if (!$a) {
        return null;
    }
    if ($a->answer_json) {
        $d = json_decode($a->answer_json, true);
        return is_array($d) ? implode(', ', $d) : $a->answer_json;
    }
    return $a->answer_text;
};
$profile = $model->profile;
$currentStatusLabel = $statusLabels[$model->status] ?? $model->status;
$currentTone = $statusTone[$model->status] ?? 'slate';
$completionPercent = $requiredDocumentCount > 0 ? min(100, (int) round(($uploadedRequiredCount / $requiredDocumentCount) * 100)) : 0;
$validPercent = $requiredDocumentCount > 0 ? min(100, (int) round(($validRequiredCount / $requiredDocumentCount) * 100)) : 0;
$documentStateCounts = ['pending' => 0, 'valid' => 0, 'invalid' => 0];
foreach ($documents as $documentRows) {
    foreach ((array) $documentRows as $documentRow) {
        $state = $documentRow->verification_status ?: 'pending';
        if (isset($documentStateCounts[$state])) {
            $documentStateCounts[$state]++;
        }
    }
}

$workflowStages = [
    ['code' => 'submitted', 'label' => 'Verifikasi Berkas', 'icon' => 'fa-search'],
    ['code' => 'verified', 'label' => 'Terverifikasi', 'icon' => 'fa-check'],
    ['code' => 'administration_pass', 'label' => 'Lolos Administrasi', 'icon' => 'fa-file-text-o'],
    ['code' => 'interview', 'label' => 'Wawancara', 'icon' => 'fa-comments-o'],
    ['code' => 'interview_pass', 'label' => 'Lolos Wawancara', 'icon' => 'fa-star-o'],
    ['code' => 'final_pass', 'label' => 'Lolos Akhir', 'icon' => 'fa-trophy'],
];
$workflowOrder = array_column($workflowStages, 'code');
$currentWorkflowCode = $model->status;
if ($currentWorkflowCode === 'revision_required') {
    $currentWorkflowCode = 'submitted';
}
$currentWorkflowIndex = array_search($currentWorkflowCode, $workflowOrder, true);
if ($currentWorkflowIndex === false) {
    $currentWorkflowIndex = -1;
}
?>

<div class="review-page">
    <div class="review-hero box">
        <div class="review-hero__left">
            <div class="review-hero__eyebrow">Review Pendaftar</div>
            <h2 class="review-hero__name"><?= Html::encode($model->user->nama ?: ($profile->nama_lengkap ?? 'Peserta')) ?></h2>
            <div class="review-hero__meta">
                <span><b><?= Html::encode($model->application_code) ?></b></span>
                <span>Batch <?= (int) $model->batch->batch_number ?></span>
                <span><?= Html::encode($model->batch->getLocationLabel()) ?></span>
            </div>
        </div>
        <div class="review-hero__right">
            <span class="gsm-status-badge tone-<?= Html::encode($currentTone) ?>"><?= Html::encode($currentStatusLabel) ?></span>
            <a href="<?= Url::to(['index']) ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Kembali</a>
        </div>
    </div>

    <div class="review-quicknav box">
        <a href="#identitas"><i class="fa fa-user"></i> Identitas</a>
        <a href="#jawaban"><i class="fa fa-list-alt"></i> Jawaban</a>
        <a href="#dokumen"><i class="fa fa-folder-open-o"></i> Dokumen</a>
        <a href="#keputusan"><i class="fa fa-check-square-o"></i> Keputusan</a>
        <a href="#catatan"><i class="fa fa-sticky-note-o"></i> Catatan</a>
    </div>

    <div class="review-summary-grid">
        <div class="review-summary-card">
            <div class="review-summary-card__icon is-upload"><i class="fa fa-cloud-upload"></i></div>
            <div>
                <div class="review-summary-card__label">Dokumen Terunggah</div>
                <div class="review-summary-card__value"><?= (int) $uploadedRequiredCount ?><span>/<?= (int) $requiredDocumentCount ?></span></div>
            </div>
        </div>
        <div class="review-summary-card">
            <div class="review-summary-card__icon is-pending"><i class="fa fa-clock-o"></i></div>
            <div>
                <div class="review-summary-card__label">Belum Diperiksa</div>
                <div class="review-summary-card__value"><?= (int) $documentStateCounts['pending'] ?></div>
            </div>
        </div>
        <div class="review-summary-card">
            <div class="review-summary-card__icon is-valid"><i class="fa fa-check"></i></div>
            <div>
                <div class="review-summary-card__label">Dokumen Valid</div>
                <div class="review-summary-card__value"><?= (int) $documentStateCounts['valid'] ?></div>
            </div>
        </div>
        <div class="review-summary-card">
            <div class="review-summary-card__icon is-revision"><i class="fa fa-exclamation"></i></div>
            <div>
                <div class="review-summary-card__label">Perlu Revisi</div>
                <div class="review-summary-card__value"><?= (int) $documentStateCounts['invalid'] ?></div>
            </div>
        </div>
    </div>

    <div class="review-workflow box">
        <div class="review-workflow__head">
            <div>
                <div class="review-workflow__eyebrow">Alur Seleksi</div>
                <div class="review-workflow__title">Posisi peserta saat ini</div>
            </div>
            <?php if ($model->status === 'revision_required'): ?>
                <span class="gsm-state-pill tone-red">Menunggu Perbaikan</span>
            <?php elseif ($model->status === 'rejected'): ?>
                <span class="gsm-state-pill tone-red">Proses Berhenti</span>
            <?php else: ?>
                <span class="gsm-status-badge tone-<?= Html::encode($currentTone) ?> small"><?= Html::encode($currentStatusLabel) ?></span>
            <?php endif; ?>
        </div>
        <div class="review-workflow__track">
            <?php foreach ($workflowStages as $stageIndex => $stage): ?>
                <?php
                    $stageClass = 'is-upcoming';
                    if ($model->status === 'rejected') {
                        $stageClass = 'is-muted';
                    } elseif ($stageIndex < $currentWorkflowIndex) {
                        $stageClass = 'is-complete';
                    } elseif ($stageIndex === $currentWorkflowIndex) {
                        $stageClass = 'is-current';
                    }
                ?>
                <div class="workflow-step <?= Html::encode($stageClass) ?>">
                    <div class="workflow-step__dot"><i class="fa <?= Html::encode($stage['icon']) ?>"></i></div>
                    <div class="workflow-step__content">
                        <span class="workflow-step__number"><?= (int) $stageIndex + 1 ?></span>
                        <span class="workflow-step__label"><?= Html::encode($stage['label']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="box box-success" id="identitas">
                <div class="box-header with-border"><h3 class="box-title">Identitas Peserta</h3></div>
                <div class="box-body">
                    <div class="identity-grid">
                        <div class="identity-card">
                            <span class="identity-label">WhatsApp</span>
                            <span class="identity-value"><?= Html::encode($model->user->whatsapp ?: ($profile->nomor_whatsapp ?? '-')) ?></span>
                        </div>
                        <div class="identity-card">
                            <span class="identity-label">Domisili</span>
                            <span class="identity-value"><?= Html::encode($profile ? $profile->getDomisiliLabel() : '-') ?></span>
                        </div>
                        <div class="identity-card">
                            <span class="identity-label">Email</span>
                            <span class="identity-value"><?= Html::encode($model->user->email ?: '-') ?></span>
                        </div>
                        <div class="identity-card">
                            <span class="identity-label">Jenjang</span>
                            <span class="identity-value"><?= Html::encode($profile->pendidikan_terakhir ?? '-') ?></span>
                        </div>
                        <div class="identity-card">
                            <span class="identity-label">Jenis Kelamin</span>
                            <span class="identity-value"><?= Html::encode($profile->jenis_kelamin ?? '-') ?></span>
                        </div>
                        <div class="identity-card">
                            <span class="identity-label">Instansi</span>
                            <span class="identity-value"><?= Html::encode($profile->asal_instansi ?? '-') ?></span>
                        </div>
                        <div class="identity-card">
                            <span class="identity-label">TTL</span>
                            <span class="identity-value"><?= Html::encode(($profile->tempat_lahir ?? '-') . ($profile && $profile->tanggal_lahir ? ', ' . Yii::$app->formatter->asDate($profile->tanggal_lahir) : '')) ?></span>
                        </div>
                        <div class="identity-card">
                            <span class="identity-label">Pekerjaan / Aktivitas</span>
                            <span class="identity-value"><?= Html::encode($profile->pekerjaan ?? '-') ?></span>
                        </div>
                    </div>
                    <?php if ($profile): ?>
                        <div class="identity-address">
                            <div class="identity-label">Alamat Domisili</div>
                            <div class="identity-address__value"><?= nl2br(Html::encode($profile->alamat_domisili ?: '-')) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="box" id="jawaban">
                <div class="box-header with-border"><h3 class="box-title">Jawaban Formulir</h3></div>
                <div class="box-body">
                    <?php $hasAnswer = false; foreach ($fields as $field): if ($field->isFileField()) continue; $hasAnswer = true; $answer = $getAnswer($field, $answers); ?>
                        <div class="answer-item">
                            <div class="answer-item__label"><?= Html::encode($field->label) ?></div>
                            <div class="answer-item__value"><?= $answer !== null && trim((string) $answer) !== '' ? nl2br(Html::encode($answer)) : '<span class="text-muted">Belum dijawab</span>' ?></div>
                        </div>
                    <?php endforeach; if (!$hasAnswer): ?><p class="text-muted">Belum ada pertanyaan seleksi.</p><?php endif; ?>
                </div>
            </div>

            <div class="box box-warning" id="dokumen">
                <div class="box-header with-border">
                    <h3 class="box-title">Dokumen & Bukti Upload</h3>
                    <div class="box-tools review-doc-stats">
                        <span class="review-mini-pill"><?= (int) $uploadedRequiredCount ?>/<?= (int) $requiredDocumentCount ?> terunggah</span>
                        <span class="review-mini-pill is-success"><?= (int) $validRequiredCount ?> valid</span>
                    </div>
                </div>
                <div class="box-body">
                    <div class="doc-overview">
                        <div class="doc-overview__card">
                            <div class="doc-overview__label">Kelengkapan Upload</div>
                            <div class="doc-overview__value"><?= (int) $completionPercent ?>%</div>
                            <div class="doc-overview__bar"><span style="width: <?= (int) $completionPercent ?>%"></span></div>
                        </div>
                        <div class="doc-overview__card">
                            <div class="doc-overview__label">Dokumen Valid</div>
                            <div class="doc-overview__value"><?= (int) $validPercent ?>%</div>
                            <div class="doc-overview__bar is-success"><span style="width: <?= (int) $validPercent ?>%"></span></div>
                        </div>
                    </div>

                    <?php foreach ($fields as $field): if (!$field->isFileField()) continue; $rows = $documents[$field->id] ?? []; $isMulti = $field->field_type === 'multi_file'; ?>
                        <?php
                            $validation = json_decode((string) $field->validation_json, true) ?: [];
                            $maxFiles = $isMulti ? max(1, (int) ($validation['maxFiles'] ?? $validation['max_files'] ?? 5)) : 1;
                        ?>
                        <div class="verify-field-card">
                            <div class="verify-field-head">
                                <div>
                                    <div class="verify-field-title"><?= Html::encode($field->label) ?><?= $field->is_required ? ' *' : '' ?></div>
                                    <?php if ($isMulti): ?><small>Semua bukti ditampilkan satu per satu agar mudah diverifikasi.</small><?php endif; ?>
                                </div>
                                <div class="verify-field-side">
                                    <?php if ($isMulti): ?><span class="review-mini-pill"><?= count($rows) ?>/<?= (int) $maxFiles ?> bukti</span><?php endif; ?>
                                    <?php if (!$rows): ?><span class="gsm-state-pill tone-red">Belum Upload</span><?php endif; ?>
                                </div>
                            </div>

                            <?php if (!$rows): ?><p class="text-muted" style="margin:10px 0 0">Belum diunggah.</p><?php endif; ?>

                            <div class="<?= $isMulti ? 'verify-proof-grid' : '' ?>">
                                <?php foreach ($rows as $docIndex => $doc): ?>
                                    <?php
                                        $state = ['pending' => 'Belum Diperiksa', 'valid' => 'Valid', 'invalid' => 'Perlu Revisi'][$doc->verification_status] ?? $doc->verification_status;
                                        $stateTone = ['pending' => 'amber', 'valid' => 'green', 'invalid' => 'red'][$doc->verification_status] ?? 'slate';
                                        $previewUrl = Url::to(['/admin-applicant/document', 'id' => $model->id, 'documentId' => $doc->id]);
                                        $mime = (string) ($doc->mime_type ?: '');
                                        $isImage = str_starts_with($mime, 'image/') || preg_match('/\.(jpg|jpeg|png|webp)$/i', (string) $doc->original_name);
                                        $isPdf = $mime === 'application/pdf' || preg_match('/\.pdf$/i', (string) $doc->original_name);
                                    ?>
                                    <div class="verify-proof-item">
                                        <div class="verify-proof-topline">
                                            <?php if ($isMulti): ?><div class="verify-proof-number">Bukti <?= (int) $docIndex + 1 ?></div><?php endif; ?>
                                            <span class="gsm-state-pill tone-<?= Html::encode($stateTone) ?>"><?= Html::encode($state) ?></span>
                                        </div>
                                        <div class="verify-proof-file"><i class="fa fa-file-o"></i> <b><?= Html::encode($doc->original_name ?: 'Dokumen') ?></b></div>
                                        <small class="text-muted"><?= Html::encode($doc->mime_type ?: '-') ?> · <?= $doc->file_size ? number_format($doc->file_size / 1024, 0) . ' KB' : '-' ?></small>

                                        <div class="verify-proof-actions">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-default js-document-preview"
                                                data-url="<?= Html::encode($previewUrl) ?>"
                                                data-name="<?= Html::encode($doc->original_name ?: 'Dokumen') ?>"
                                                data-kind="<?= $isImage ? 'image' : ($isPdf ? 'pdf' : 'other') ?>"
                                            ><i class="fa fa-eye"></i> Pratinjau</button>
                                        </div>

                                        <?php if ($doc->verification_note): ?><div class="text-danger verify-existing-note"><small><?= Html::encode($doc->verification_note) ?></small></div><?php endif; ?>

                                        <div class="verify-proof-form">
                                            <?= Html::beginForm(['document-status', 'id' => $model->id, 'documentId' => $doc->id], 'post') ?>
                                            <div class="row">
                                                <div class="col-sm-5"><?= Html::dropDownList('verification_status', $doc->verification_status, ['pending' => 'Belum Diperiksa', 'valid' => 'Valid', 'invalid' => 'Perlu Revisi'], ['class' => 'form-control input-sm']) ?></div>
                                                <div class="col-sm-7"><button class="btn btn-primary btn-sm btn-block" type="submit"><i class="fa fa-save"></i> Simpan Pemeriksaan</button></div>
                                            </div>
                                            <?= Html::textarea('verification_note', $doc->verification_note, ['class' => 'form-control input-sm', 'rows' => 2, 'placeholder' => 'Catatan jika perlu revisi', 'style' => 'margin-top:8px']) ?>
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

        <div class="col-md-4">
            <div class="review-sidebar">
                <div class="box box-primary review-decision-box" id="keputusan">
                    <div class="box-header with-border"><h3 class="box-title">Keputusan Seleksi</h3></div>
                    <div class="box-body">
                        <div class="decision-current">
                            <div>
                                <div class="decision-current__label">Status Saat Ini</div>
                                <div class="decision-current__value"><?= Html::encode($currentStatusLabel) ?></div>
                            </div>
                            <span class="gsm-status-badge tone-<?= Html::encode($currentTone) ?> small"><?= Html::encode($model->status) ?></span>
                        </div>

                        <div class="decision-helper">
                            <i class="fa fa-info-circle"></i>
                            Pilih hasil pemeriksaan berikutnya. Panel tetap terlihat saat halaman di-scroll supaya keputusan bisa disimpan tanpa kembali ke bagian bawah halaman.
                        </div>

                        <?php if ($model->status === 'draft'): ?>
                            <div class="alert alert-warning" style="margin-bottom:0">Peserta belum mengirim formulir.</div>
                        <?php elseif ($model->status === 'revision_required'): ?>
                            <div class="alert alert-warning" style="margin-bottom:0">Menunggu peserta mengirim ulang perbaikan berkas.</div>
                        <?php elseif (!$allowedStatusOptions): ?>
                            <div class="alert alert-info" style="margin-bottom:0">Proses seleksi peserta ini sudah selesai.</div>
                        <?php else: ?>
                            <?= Html::beginForm(['status', 'id' => $model->id], 'post', ['id' => 'statusDecisionForm']) ?>
                            <div class="decision-option-list">
                                <?php foreach ($allowedStatusOptions as $code => $label): ?>
                                    <?php $tone = $statusTone[$code] ?? 'slate'; ?>
                                    <label class="decision-option tone-<?= Html::encode($tone) ?>">
                                        <input type="radio" name="status" value="<?= Html::encode($code) ?>">
                                        <span class="decision-option__icon"><i class="fa fa-check"></i></span>
                                        <span class="decision-option__body">
                                            <span class="decision-option__title"><?= Html::encode($label) ?></span>
                                            <span class="decision-option__desc"><?php
                                                $decisionDescriptions = [
                                                    'revision_required' => 'Kembalikan ke peserta untuk melengkapi atau memperbaiki data.',
                                                    'verified' => 'Semua data dan berkas sudah sesuai untuk diproses.',
                                                    'administration_pass' => 'Peserta dinyatakan lolos seleksi administrasi.',
                                                    'interview' => 'Peserta dilanjutkan ke tahap wawancara.',
                                                    'interview_pass' => 'Peserta dinyatakan lolos tahap wawancara.',
                                                    'final_pass' => 'Peserta dinyatakan lolos sebagai peserta akhir.',
                                                    'rejected' => 'Peserta tidak dilanjutkan ke tahap berikutnya.',
                                                ];
                                                echo Html::encode($decisionDescriptions[$code] ?? 'Lanjutkan sesuai hasil pemeriksaan.');
                                            ?></span>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>

                            <div class="form-group" style="margin-top:14px;margin-bottom:0">
                                <label>Catatan Verifikator</label>
                                <?= Html::textarea('status_note', '', ['class' => 'form-control', 'rows' => 4, 'placeholder' => 'Wajib diisi untuk Perlu Perbaikan / Belum Lolos']) ?>
                            </div>
                            <div class="decision-footer">
                                <button class="btn btn-success btn-lg btn-block" type="submit"><i class="fa fa-check-circle"></i> Simpan Keputusan</button>
                            </div>
                            <?= Html::endForm() ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="box" id="catatan">
                    <div class="box-header with-border"><h3 class="box-title">Catatan Internal Tim</h3></div>
                    <div class="box-body">
                        <?= Html::beginForm(['note', 'id' => $model->id], 'post') ?>
                        <?= Html::textarea('note', '', ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Catatan hanya untuk petugas']) ?>
                        <button class="btn btn-default btn-block" style="margin-top:8px" type="submit">Tambah Catatan</button>
                        <?= Html::endForm() ?>
                        <hr>
                        <?php if (!$model->notes): ?><p class="text-muted">Belum ada catatan.</p><?php endif; ?>
                        <?php foreach ($model->notes as $note): ?>
                            <div class="team-note-item">
                                <b><?= Html::encode($note->user ? ($note->user->nama ?: $note->user->username) : 'Tim') ?></b><br>
                                <small class="text-muted"><?= Yii::$app->formatter->asDatetime($note->created_at) ?></small>
                                <p><?= nl2br(Html::encode($note->note)) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
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
.review-page{padding-bottom:18px}
.review-hero{display:flex;justify-content:space-between;align-items:flex-start;gap:18px;padding:18px 20px;margin-bottom:14px;border:1px solid #e8ece9;border-radius:16px;background:linear-gradient(135deg,#ffffff 0%,#f7fbf8 100%);box-shadow:0 10px 28px rgba(18,42,28,.06)}
.review-hero__eyebrow{font-size:11px;letter-spacing:1.2px;text-transform:uppercase;color:#14824b;font-weight:800;margin-bottom:6px}.review-hero__name{margin:0 0 8px;font-size:34px;line-height:1.15;font-weight:800;color:#213126}.review-hero__meta{display:flex;flex-wrap:wrap;gap:8px;color:#647269}.review-hero__meta span{padding:7px 10px;border-radius:999px;background:#eef5f0;font-size:12px}.review-hero__right{display:flex;flex-direction:column;align-items:flex-end;gap:10px}
.review-quicknav{display:flex;flex-wrap:wrap;gap:9px;padding:12px 14px;margin-bottom:14px;border-radius:14px;border:1px solid #e7ece8;background:#fff}.review-quicknav a{display:inline-flex;align-items:center;gap:7px;padding:9px 12px;border-radius:10px;background:#f6f8f7;color:#304035;font-weight:600}.review-quicknav a:hover{background:#edf5ef;text-decoration:none}
.review-summary-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-bottom:14px}.review-summary-card{display:flex;align-items:center;gap:12px;padding:15px 16px;border:1px solid #e7ece8;border-radius:14px;background:#fff;box-shadow:0 8px 22px rgba(18,42,28,.045)}.review-summary-card__icon{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:16px;flex:0 0 auto}.review-summary-card__icon.is-upload{background:#edf5ff;color:#2f69b4}.review-summary-card__icon.is-pending{background:#fff5de;color:#a66a06}.review-summary-card__icon.is-valid{background:#e9f8ef;color:#167846}.review-summary-card__icon.is-revision{background:#fdefed;color:#ae4940}.review-summary-card__label{font-size:11px;text-transform:uppercase;letter-spacing:.65px;color:#7b8880;font-weight:800}.review-summary-card__value{margin-top:2px;font-size:25px;line-height:1.1;font-weight:900;color:#203027}.review-summary-card__value span{font-size:13px;color:#89948d;font-weight:700;margin-left:2px}
.review-workflow{padding:16px 18px;margin-bottom:16px;border:1px solid #e7ece8;border-radius:16px;background:linear-gradient(135deg,#fff 0%,#fafcfb 100%);box-shadow:0 8px 24px rgba(18,42,28,.045)}.review-workflow__head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px}.review-workflow__eyebrow{font-size:10px;letter-spacing:1px;text-transform:uppercase;color:#16824a;font-weight:900}.review-workflow__title{font-size:16px;color:#243128;font-weight:800;margin-top:2px}.review-workflow__track{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));position:relative}.workflow-step{position:relative;display:flex;flex-direction:column;align-items:center;text-align:center;min-width:0}.workflow-step:not(:last-child):after{content:'';position:absolute;top:18px;left:calc(50% + 20px);right:calc(-50% + 20px);height:2px;background:#e4e9e6;z-index:0}.workflow-step.is-complete:not(:last-child):after{background:#9fd6b4}.workflow-step__dot{position:relative;z-index:1;width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#f1f4f2;border:2px solid #dfe6e1;color:#89968e;font-size:13px}.workflow-step__content{margin-top:8px;display:flex;flex-direction:column;gap:2px;min-width:0}.workflow-step__number{font-size:9px;text-transform:uppercase;color:#9aa49e;font-weight:800}.workflow-step__label{font-size:11px;line-height:1.25;color:#67746c;font-weight:700}.workflow-step.is-complete .workflow-step__dot{background:#e8f7ef;border-color:#b9e2c8;color:#177846}.workflow-step.is-complete .workflow-step__label{color:#34734e}.workflow-step.is-current .workflow-step__dot{background:#16824a;border-color:#16824a;color:#fff;box-shadow:0 0 0 5px rgba(22,130,74,.11)}.workflow-step.is-current .workflow-step__label{color:#1e3226;font-weight:900}.workflow-step.is-current .workflow-step__number{color:#16824a}.workflow-step.is-muted{opacity:.45}
.gsm-status-badge{display:inline-flex;align-items:center;gap:8px;padding:9px 14px;border-radius:999px;font-weight:800;font-size:12px;border:1px solid transparent}.gsm-status-badge.small{font-size:11px;padding:7px 11px}
.gsm-status-badge:before{content:'';width:8px;height:8px;border-radius:50%;background:currentColor;opacity:.7}.tone-slate{color:#5b6670;background:#eef2f5;border-color:#d9e1e8}.tone-amber{color:#9a6500;background:#fff4d8;border-color:#f4dfac}.tone-orange{color:#a04a14;background:#ffeadc;border-color:#f8cfb8}.tone-blue{color:#245ea8;background:#e7f0ff;border-color:#c9dbff}.tone-green{color:#167846;background:#e8f7ef;border-color:#c7ead4}.tone-purple{color:#6e42b7;background:#efe7fb;border-color:#dac8f7}.tone-teal{color:#136b74;background:#e4f7f9;border-color:#c5e9ed}.tone-red{color:#a73737;background:#fdeaea;border-color:#f3c7c7}
.identity-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.identity-card{padding:14px;border:1px solid #e9eeeb;border-radius:12px;background:#fbfcfb}.identity-label{display:block;font-size:11px;text-transform:uppercase;letter-spacing:.7px;color:#7a877f;font-weight:700;margin-bottom:4px}.identity-value{display:block;font-size:15px;line-height:1.45;color:#243128;font-weight:600;word-break:break-word}.identity-address{margin-top:14px;padding:14px;border-radius:12px;background:#f8fbf9;border:1px solid #e8efea}.identity-address__value{margin-top:6px;white-space:pre-line;color:#304035}
.answer-item{padding:14px 0;border-bottom:1px solid #edf1ee}.answer-item:last-child{border-bottom:0;padding-bottom:0}.answer-item__label{font-weight:800;color:#233126;margin-bottom:6px}.answer-item__value{white-space:pre-line;color:#3e4c43;line-height:1.65}
.review-doc-stats{display:flex;gap:6px;align-items:center}.review-mini-pill{display:inline-flex;align-items:center;padding:5px 10px;border-radius:999px;background:#edf2ef;color:#4c5d53;font-size:11px;font-weight:700}.review-mini-pill.is-success{background:#e7f7ee;color:#177846}
.doc-overview{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-bottom:14px}.doc-overview__card{padding:14px;border:1px solid #e8efea;border-radius:12px;background:#fbfcfb}.doc-overview__label{font-size:12px;color:#728077;font-weight:700}.doc-overview__value{font-size:28px;font-weight:800;color:#1b2f24;margin:3px 0 10px}.doc-overview__bar{height:8px;border-radius:999px;background:#edf2ef;overflow:hidden}.doc-overview__bar span{display:block;height:100%;border-radius:999px;background:linear-gradient(90deg,#f0c15d,#e48b2d)}.doc-overview__bar.is-success span{background:linear-gradient(90deg,#67bf84,#1d8c54)}
.verify-field-card{border:1px solid #e4ebe6;border-radius:14px;padding:16px;margin-bottom:14px;background:#fff;box-shadow:0 8px 22px rgba(21,39,28,.04)}.verify-field-head{display:flex;justify-content:space-between;gap:12px;align-items:flex-start;margin-bottom:10px}.verify-field-title{font-size:16px;font-weight:800;color:#223026}.verify-field-head small{display:block;color:#819087;margin-top:4px}.verify-field-side{display:flex;gap:7px;flex-wrap:wrap;justify-content:flex-end}
.verify-proof-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-top:10px}.verify-proof-item{border:1px solid #edf1ee;border-radius:12px;padding:14px;background:#fbfcfc}.verify-proof-topline{display:flex;justify-content:space-between;align-items:center;gap:8px;margin-bottom:8px}.verify-proof-number{display:inline-flex;padding:4px 9px;border-radius:999px;background:#e8f6ee;color:#177846;font-size:11px;font-weight:800}.gsm-state-pill{display:inline-flex;padding:4px 9px;border-radius:999px;font-size:11px;font-weight:800;border:1px solid transparent}.gsm-state-pill.tone-slate{color:#58636c;background:#edf1f4;border-color:#dde3e8}.gsm-state-pill.tone-amber{color:#9a6500;background:#fff4d8;border-color:#f4dfac}.gsm-state-pill.tone-green{color:#177846;background:#e8f7ef;border-color:#c7ead4}.gsm-state-pill.tone-red{color:#a73737;background:#fdeaea;border-color:#f3c7c7}
.verify-proof-file{color:#243128;overflow-wrap:anywhere;margin-bottom:3px}.verify-proof-actions{margin-top:10px}.verify-existing-note{margin-top:8px}.verify-proof-form{margin-top:12px;padding-top:12px;border-top:1px dashed #dde5df}
.review-sidebar{position:sticky;top:18px}.review-decision-box{border-radius:16px;box-shadow:0 12px 30px rgba(18,42,28,.08)}.decision-current{display:flex;align-items:flex-start;justify-content:space-between;gap:10px;padding:12px 14px;border:1px solid #ebf0ec;border-radius:12px;background:#fbfcfb;margin-bottom:12px}.decision-current__label{font-size:11px;letter-spacing:.8px;text-transform:uppercase;color:#7d897f;font-weight:800}.decision-current__value{font-size:20px;font-weight:800;color:#1f2f24;margin-top:4px;line-height:1.2}.decision-helper{display:flex;gap:8px;align-items:flex-start;padding:11px 12px;border-radius:12px;background:#f6f9f7;color:#59665f;font-size:13px;line-height:1.5;margin-bottom:14px}.decision-helper i{margin-top:2px;color:#14824b}
.decision-option-list{display:flex;flex-direction:column;gap:10px}.decision-option{position:relative;display:flex;align-items:flex-start;gap:12px;padding:13px 14px;border:1px solid #e3eae5;border-radius:12px;background:#fff;cursor:pointer;transition:.18s ease}.decision-option:hover{transform:translateY(-1px);box-shadow:0 10px 18px rgba(21,39,28,.06)}.decision-option input{position:absolute;opacity:0;pointer-events:none}.decision-option__icon{width:22px;height:22px;border-radius:50%;border:2px solid #c9d3cc;display:flex;align-items:center;justify-content:center;color:transparent;flex:0 0 auto;margin-top:2px;background:#fff}.decision-option__title{display:block;font-weight:800;color:#223026}.decision-option__desc{display:block;font-size:12px;color:#7a877f;margin-top:2px}.decision-option.is-selected .decision-option__icon{color:#fff;border-color:transparent}.decision-option.is-selected .decision-option__title{color:#14261b}
.decision-option.tone-green.is-selected{background:#edf9f2;border-color:#bfe7cd}.decision-option.tone-green.is-selected .decision-option__icon{background:#1b8a53}.decision-option.tone-blue.is-selected{background:#eef5ff;border-color:#c6dcff}.decision-option.tone-blue.is-selected .decision-option__icon{background:#2f69b4}.decision-option.tone-orange.is-selected{background:#fff2e8;border-color:#f3cfb7}.decision-option.tone-orange.is-selected .decision-option__icon{background:#c96c2c}.decision-option.tone-red.is-selected{background:#feefef;border-color:#f3c9c9}.decision-option.tone-red.is-selected .decision-option__icon{background:#c54a4a}.decision-option.tone-purple.is-selected{background:#f3ecff;border-color:#ddcef9}.decision-option.tone-purple.is-selected .decision-option__icon{background:#7646be}.decision-option.tone-teal.is-selected{background:#eaf9fb;border-color:#c5e8ed}.decision-option.tone-teal.is-selected .decision-option__icon{background:#1b7c85}
.decision-footer{margin-top:14px;padding-top:12px;border-top:1px solid #edf1ee}.team-note-item{padding:10px 0;border-bottom:1px solid #edf1ee}.team-note-item:last-child{border-bottom:0;padding-bottom:0}.team-note-item p{margin:5px 0 0;white-space:pre-line}
.gsm-document-modal{position:fixed;inset:0;z-index:99999;display:none;align-items:center;justify-content:center;padding:24px}.gsm-document-modal.is-open{display:flex}.gsm-document-modal__backdrop{position:absolute;inset:0;background:rgba(9,25,17,.72);backdrop-filter:blur(3px)}
.gsm-document-modal__dialog{position:relative;width:min(980px,96vw);height:min(820px,90vh);display:flex;flex-direction:column;background:#fff;border-radius:16px;box-shadow:0 30px 90px rgba(0,0,0,.35);overflow:hidden}.gsm-document-modal__head{display:flex;align-items:center;justify-content:space-between;gap:18px;padding:15px 18px;border-bottom:1px solid #e9eeeb}.gsm-document-modal__head small{color:#16824a;font-size:9px;font-weight:800;letter-spacing:.8px}.gsm-document-modal__head h4{margin:2px 0 0;font-size:15px}.gsm-document-modal__close{width:36px;height:36px;border:0;border-radius:50%;background:#eff4f1;color:#26362d;font-size:24px;line-height:1;cursor:pointer}
.gsm-document-modal__body{flex:1;min-height:0;display:flex;align-items:center;justify-content:center;padding:14px;background:#f5f7f5;overflow:auto}.gsm-document-modal__body img{display:block;max-width:100%;max-height:100%;object-fit:contain;border-radius:8px;background:#fff}.gsm-document-modal__body iframe{width:100%;height:100%;min-height:620px;border:0;border-radius:8px;background:#fff}.gsm-document-modal__fallback{text-align:center;color:#65736a}.gsm-document-modal__fallback a{display:inline-block;margin-left:7px;font-weight:700;color:#147343}
body.gsm-modal-open{overflow:hidden}
@media(max-width:991px){.review-sidebar{position:static}.review-hero{flex-direction:column;align-items:flex-start}.review-hero__right{align-items:flex-start}.identity-grid,.doc-overview,.verify-proof-grid{grid-template-columns:1fr}.review-summary-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.review-workflow__track{overflow-x:auto;display:flex;padding-bottom:5px}.workflow-step{min-width:125px}.workflow-step:not(:last-child):after{left:calc(50% + 20px);right:auto;width:85px}}
@media(max-width:767px){.review-hero{padding:16px}.review-hero__name{font-size:28px}.review-quicknav{padding:10px}.review-quicknav a{font-size:12px;padding:8px 10px}.review-summary-grid{grid-template-columns:1fr 1fr;gap:8px}.review-summary-card{padding:12px}.review-summary-card__icon{width:36px;height:36px}.review-summary-card__value{font-size:21px}.review-workflow{padding:14px}.review-workflow__head{align-items:flex-start;flex-direction:column}.gsm-document-modal{padding:10px}.gsm-document-modal__dialog{width:100%;height:92vh}.gsm-document-modal__body iframe{min-height:520px}}
CSS);

$this->registerJs(<<<'JS'
(function(){
    const modal=document.getElementById('gsmDocumentModal');
    const decisionForm=document.getElementById('statusDecisionForm');
    if(modal){
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
    }

    if(decisionForm){
        const radios=decisionForm.querySelectorAll('input[type="radio"][name="status"]');
        const note=decisionForm.querySelector('textarea[name="status_note"]');
        const syncSelected=function(){
            decisionForm.querySelectorAll('.decision-option').forEach(function(card){
                const input=card.querySelector('input[type="radio"]');
                card.classList.toggle('is-selected', !!input.checked);
            });
        };
        radios.forEach(function(input){
            input.addEventListener('change', function(){
                syncSelected();
                if(note && (input.value==='revision_required' || input.value==='rejected')){
                    note.focus();
                }
            });
        });
        syncSelected();
    }
})();
JS);
?>
