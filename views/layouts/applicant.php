<?php
use app\assets\GuestAsset;
use yii\helpers\Html;
use yii\helpers\Url;

GuestAsset::register($this);
$user = Yii::$app->user->identity;
$logoPath = Yii::getAlias('@webroot') . '/web/assets-guest/img/logo-horizontal.png';
$hasLogo = is_file($logoPath);
$currentRoute = Yii::$app->controller->route;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title ?: 'Portal Peserta — Sumut Mengajar') ?></title>
    <meta name="robots" content="noindex,nofollow,noarchive">
    <?php $this->head() ?>
</head>
<body class="gsm-portal portal-v2">
<?php $this->beginBody() ?>

<header class="portal-v2-topbar">
    <div class="portal-v2-wrap portal-v2-topbar-inner">
        <a class="portal-v2-brand" href="<?= Url::to(['/site/index']) ?>">
            <?php if ($hasLogo): ?>
                <img src="<?= Yii::$app->request->baseUrl ?>/web/assets-guest/img/logo-horizontal.png" alt="Sumut Mengajar">
            <?php else: ?>
                <span class="portal-v2-logo-mark">SM</span>
                <span class="portal-v2-wordmark"><b>sumut</b>mengajar</span>
            <?php endif; ?>
        </a>

        <div class="portal-v2-userbar">
            <div class="portal-v2-usercopy">
                <strong><?= Html::encode($user->nama ?: 'Peserta Sumut Mengajar') ?></strong>
                <span><?= Html::encode($user->whatsapp ? ('+' . ltrim($user->whatsapp, '+')) : ($user->email ?: '')) ?></span>
            </div>
            <span class="portal-v2-avatar"><?= Html::encode(strtoupper(substr($user->nama ?: $user->email ?: 'S', 0, 1))) ?></span>
            <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'portal-v2-logout-form']) ?>
                <?= Html::submitButton('Keluar', ['class' => 'portal-v2-logout']) ?>
            <?= Html::endForm() ?>
        </div>
    </div>
</header>

<div class="portal-v2-wrap portal-v2-shell">
    <aside class="portal-v2-sidebar">
        <div class="portal-v2-sidebar-label">PORTAL PESERTA</div>
        <nav class="portal-v2-menu">
            <a class="<?= str_starts_with($currentRoute, 'applicant/dashboard') ? 'active' : '' ?>" href="<?= Url::to(['/applicant/dashboard']) ?>">
                <span class="portal-v2-menu-icon">⌂</span><span>Dashboard</span>
            </a>
            <a class="<?= str_starts_with($currentRoute, 'applicant/application') ? 'active' : '' ?>" href="<?= Url::to(['/applicant/dashboard', '#' => 'pendaftaran-saya']) ?>">
                <span class="portal-v2-menu-icon">✓</span><span>Pendaftaran Saya</span>
            </a>
            <a href="<?= Url::to(['/recruitment/index']) ?>">
                <span class="portal-v2-menu-icon">＋</span><span>Rekrutmen</span>
            </a>
            <a href="<?= Url::to(['/site/index']) ?>">
                <span class="portal-v2-menu-icon">↗</span><span>Website Utama</span>
            </a>
        </nav>

        <div class="portal-v2-help">
            <span>Butuh bantuan?</span>
            <strong>Tim Sumut Mengajar</strong>
            <small>Pastikan data dan dokumen pendaftaranmu lengkap sebelum batas waktu.</small>
        </div>
    </aside>

    <main class="portal-v2-main">
        <?php foreach (['success','error','warning'] as $type): ?>
            <?php if (Yii::$app->session->hasFlash($type)): ?>
                <div class="portal-v2-alert <?= Html::encode($type) ?>">
                    <?= Html::encode(Yii::$app->session->getFlash($type)) ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <?= $content ?>
    </main>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
