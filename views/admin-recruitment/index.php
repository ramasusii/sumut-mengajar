<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Batch Rekrutmen';

$statusLabels = [
    'draft' => 'Draf',
    'open' => 'Pendaftaran Dibuka',
    'closed' => 'Pendaftaran Ditutup',
    'announced' => 'Pengumuman Terbit',
];
?>

<p>
    <?= Html::a('<i class="fa fa-plus"></i> Tambah Batch', ['create'], ['class' => 'btn btn-primary']) ?>
</p>

<div class="box">
    <div class="box-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Batch</th>
                    <th>Lokasi</th>
                    <th>Pendaftaran</th>
                    <th>Pelaksanaan</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($models as $m): ?>
                <tr>
                    <td><?= Html::encode($m->code) ?></td>
                    <td><b><?= Html::encode($m->title) ?></b><br><small>Batch <?= (int)$m->batch_number ?></small></td>
                    <td><?= Html::encode($m->getLocationLabel()) ?></td>
                    <td><?= Yii::$app->formatter->asDate($m->registration_start) ?> – <?= Yii::$app->formatter->asDate($m->registration_end) ?></td>
                    <td>
                        <b>Pembekalan:</b> <?= Html::encode($m->getBriefingPeriodLabel()) ?><br>
                        <small><b>Pengabdian:</b> <?= Html::encode($m->getServicePeriodLabel()) ?></small>
                    </td>
                    <td><span class="label label-info"><?= Html::encode($statusLabels[$m->status] ?? 'Tidak diketahui') ?></span></td>
                    <td>
                        <?= Html::a('Edit', ['update', 'id' => $m->id], ['class' => 'btn btn-xs btn-default']) ?>
                        <?= Html::a('Lihat Halaman', ['/recruitment/view', 'slug' => $m->slug], ['class' => 'btn btn-xs btn-success', 'target' => '_blank']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
