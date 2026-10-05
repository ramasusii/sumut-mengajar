<?php
use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;

AppAsset::register($this);
dmstr\web\AdminLteAsset::register($this);
$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
$isSuperAdmin = Yii::$app->user->can('superAdmin');
$isAdminGsm = Yii::$app->user->can('adminGsm');
$isReviewer = Yii::$app->user->can('reviewer');
$roleLabel = $isSuperAdmin ? 'Super Admin' : ($isAdminGsm ? 'Admin GSM' : ($isReviewer ? 'Verifikator' : 'Petugas'));
?>
<?php $this->beginPage() ?>
<!DOCTYPE html><html lang="<?= Yii::$app->language ?>"><head><meta charset="<?= Yii::$app->charset ?>"><meta name="viewport" content="width=device-width,initial-scale=1"><?= Html::csrfMetaTags() ?><meta name="robots" content="noindex,nofollow,noarchive"><title><?= Html::encode($this->title ?: 'Portal Petugas Sumut Mengajar') ?></title><?php $this->head() ?><style>.skin-green-light .main-header .navbar,.skin-green-light .main-header .logo{background:#156c45!important}.content-wrapper{background:#f5f7f6}.box{border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,.06);border-top:0}.main-sidebar{box-shadow:4px 0 24px rgba(0,0,0,.04)}.staff-role{color:#b8c7ce;font-size:11px;margin-top:2px}.sidebar-menu .header{font-size:10px;letter-spacing:.7px}</style></head>
<body class="hold-transition skin-green-light sidebar-mini"><?php $this->beginBody() ?><div class="wrapper">
<header class="main-header"><a href="<?= Url::to(['/admin-dashboard/index']) ?>" class="logo"><span class="logo-mini"><b>SM</b></span><span class="logo-lg"><b>Sumut</b> Mengajar</span></a><nav class="navbar navbar-static-top"><a href="#" class="sidebar-toggle" data-toggle="push-menu"></a><div class="navbar-custom-menu"><ul class="nav navbar-nav"><li><a href="<?= Url::to(['/site/index']) ?>" target="_blank"><i class="fa fa-external-link"></i> Website</a></li><li><?= Html::beginForm(['/site/logout'],'post',['style'=>'display:inline']) ?><?= Html::submitButton('<i class="fa fa-sign-out"></i> Keluar',['class'=>'btn btn-link navbar-btn','style'=>'color:white;margin-right:10px']) ?><?= Html::endForm() ?></li></ul></div></nav></header>
<aside class="main-sidebar"><section class="sidebar"><div class="user-panel"><div class="pull-left image"><img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle"></div><div class="pull-left info"><p><?= Html::encode(Yii::$app->user->identity->nama ?: 'Petugas') ?></p><div class="staff-role"><?= Html::encode($roleLabel) ?></div><small><i class="fa fa-circle text-success"></i> Online</small></div></div>
<ul class="sidebar-menu" data-widget="tree"><li class="header">PORTAL PETUGAS</li><li><a href="<?= Url::to(['/admin-dashboard/index']) ?>"><i class="fa fa-dashboard"></i><span>Dashboard</span></a></li>
<?php if($isSuperAdmin||$isAdminGsm): ?><li class="header">REKRUTMEN</li><li><a href="<?= Url::to(['/admin-recruitment/index']) ?>"><i class="fa fa-calendar-check-o"></i><span>Batch Rekrutmen</span></a></li><?php endif; ?><li><a href="<?= Url::to(['/admin-applicant/index']) ?>"><i class="fa fa-users"></i><span>Pendaftar & Verifikasi</span></a></li>
<?php if($isSuperAdmin||$isAdminGsm): ?><li class="header">WEBSITE & ALUMNI</li><li><a href="<?= Url::to(['/admin-hero/index']) ?>"><i class="fa fa-picture-o"></i><span>Hero Banner</span></a></li><li><a href="<?= Url::to(['/admin-alumni/index']) ?>"><i class="fa fa-graduation-cap"></i><span>Alumni & Karya</span></a></li><?php endif; ?>
<?php if($isSuperAdmin): ?><li class="header">PENGATURAN AKSES</li><li><a href="<?= Url::to(['/admin/role']) ?>"><i class="fa fa-shield"></i><span>Peran & Hak Akses</span></a></li><li><a href="<?= Url::to(['/admin/route']) ?>"><i class="fa fa-link"></i><span>Daftar Akses</span></a></li><li><a href="<?= Url::to(['/admin/menu']) ?>"><i class="fa fa-list"></i><span>Menu Sistem</span></a></li><?php endif; ?>
</ul></section></aside>
<div class="content-wrapper"><section class="content-header"><h1><?= Html::encode($this->title ?: 'Dashboard') ?></h1></section><section class="content"><?php foreach(['success','error','warning'] as $type): ?><?php if(Yii::$app->session->hasFlash($type)): ?><div class="alert alert-<?= $type==='error'?'danger':Html::encode($type) ?>"><?= Html::encode(Yii::$app->session->getFlash($type)) ?></div><?php endif; ?><?php endforeach; ?><?= $content ?></section></div>
<footer class="main-footer"><strong>Gerakan Sumut Mengajar</strong> · sumutmengajar.org</footer></div><?php $this->endBody() ?></body></html><?php $this->endPage() ?>
