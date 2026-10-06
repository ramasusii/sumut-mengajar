<?php
use app\assets\GuestAsset;
use yii\helpers\Html;
use yii\helpers\Url;

GuestAsset::register($this);
$brand = Yii::$app->params['brand'];
$logoPath = Yii::getAlias('@app/web/assets-guest/img/logo-horizontal.png');
$logoUrl = is_file($logoPath)
    ? Yii::$app->request->baseUrl . '/web/assets-guest/img/logo-horizontal.png?v=' . filemtime($logoPath)
    : null;
$footerLogoPath = Yii::getAlias('@app/web/assets-guest/img/logo-white.png');
$footerLogoUrl = is_file($footerLogoPath)
    ? Yii::$app->request->baseUrl . '/web/assets-guest/img/logo-white.png?v=' . filemtime($footerLogoPath)
    : null;
$isStaff = !Yii::$app->user->isGuest && (
    Yii::$app->user->can('developer') || Yii::$app->user->can('superAdmin') || Yii::$app->user->can('adminGsm') || Yii::$app->user->can('reviewer')
);
$dashboardUrl = $isStaff ? Url::to(['/admin-dashboard/index']) : Url::to(['/applicant/dashboard']);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Gerakan Sumut Mengajar — Aksi Nyata Peduli Pendidikan Sumatera Utara.">
    <?= Html::csrfMetaTags() ?>
    <link rel="icon" type="image/x-icon" href="<?= Yii::$app->request->baseUrl ?>/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= Yii::$app->request->baseUrl ?>/web/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= Yii::$app->request->baseUrl ?>/web/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= Yii::$app->request->baseUrl ?>/web/apple-touch-icon.png">
    <link rel="manifest" href="<?= Yii::$app->request->baseUrl ?>/web/site.webmanifest">
    <meta name="theme-color" content="#0e623a">
    <title><?= Html::encode($this->title ?: $brand['name']) ?></title>
    <style>
        /* TRACKING HIGHLIGHT V8 */
        .gsm-nav .nav-track-highlight{
            display:inline-flex!important;
            align-items:center!important;
            gap:7px!important;
            padding:10px 15px!important;
            border-radius:999px!important;
            background:#fff5d6!important;
            border:1px solid #f2d573!important;
            color:#725100!important;
            font-weight:850!important;
            text-decoration:none!important;
            white-space:nowrap!important;
            box-shadow:0 5px 16px rgba(122,91,0,.08)!important;
            transition:.2s ease!important;
        }
        .gsm-nav .nav-track-highlight:hover{
            background:#ffeaa3!important;
            color:#5e4300!important;
            transform:translateY(-1px)!important;
        }
        .nav-track-dot{
            width:8px!important;
            height:8px!important;
            border-radius:50%!important;
            background:#f28a24!important;
            box-shadow:0 0 0 4px rgba(242,138,36,.13)!important;
            flex:0 0 auto!important;
        }

        @media(max-width:980px){
            .gsm-nav .nav-track-highlight{
                width:100%!important;
                justify-content:center!important;
                margin-top:4px!important;
            }
        }


        /* PUBLIC NAV V22.4 */
        .gsm-header .gsm-nav{
            gap:clamp(14px,1.45vw,24px)!important;
        }
        .gsm-header .gsm-nav>a{
            white-space:nowrap!important;
        }
        @media(min-width:981px) and (max-width:1180px){
            .gsm-header .gsm-nav{
                gap:12px!important;
            }
            .gsm-header .gsm-nav>a{
                font-size:12px!important;
            }
            .gsm-nav .nav-track-highlight{
                padding:9px 12px!important;
            }
        }

        /* FOOTER CLEAN V11 — tidak memakai class footer lama */
        .gsm-footer .gsm-footer-v11{
            display:flex!important;
            flex-direction:row!important;
            flex-wrap:nowrap!important;
            align-items:flex-start!important;
            justify-content:space-between!important;
            gap:clamp(28px,4vw,58px)!important;
            width:100%!important;
            padding-top:4px!important;
        }

        .gsm-footer .gsm-footer-v11__brand{
            order:1!important;
            flex:1.35 1 0!important;
            min-width:230px!important;
            margin:0!important;
            padding:0!important;
        }

        .gsm-footer .gsm-footer-v11__nav{
            order:2!important;
        }

        .gsm-footer .gsm-footer-v11__alumni{
            order:3!important;
        }

        .gsm-footer .gsm-footer-v11__contact{
            order:4!important;
        }

        .gsm-footer .gsm-footer-v11__column{
            flex:.8 1 0!important;
            min-width:145px!important;
            margin:0!important;
            padding:0!important;
            display:flex!important;
            flex-direction:column!important;
            align-items:flex-start!important;
            gap:12px!important;
        }

        .gsm-footer .gsm-footer-v11__logo{
            display:block!important;
            width:190px!important;
            max-width:190px!important;
            height:auto!important;
            max-height:72px!important;
            margin:0 0 20px 0!important;
            padding:0!important;
            object-fit:contain!important;
            object-position:left center!important;
            background:transparent!important;
            border:0!important;
            box-shadow:none!important;
        }

        .gsm-footer .gsm-footer-v11__brand p{
            margin:0!important;
            padding:0!important;
            max-width:285px!important;
            line-height:1.7!important;
        }

        .gsm-footer .gsm-footer-v11__column b{
            display:block!important;
            margin:0 0 6px 0!important;
            padding:0!important;
        }

        .gsm-footer .gsm-footer-v11__column a,
        .gsm-footer .gsm-footer-v11__column span{
            display:block!important;
            margin:0!important;
            padding:0!important;
        }

        /* FOOTER TEXT COLOR FIX V22.6
           Override warna link global/visited agar footer tidak kembali biru. */
        .gsm-footer,
        .gsm-footer p,
        .gsm-footer b,
        .gsm-footer strong,
        .gsm-footer span{
            color:rgba(255,255,255,.88)!important;
        }

        .gsm-footer .gsm-footer-v11__column a,
        .gsm-footer .gsm-footer-v11__column a:link,
        .gsm-footer .gsm-footer-v11__column a:visited{
            color:rgba(255,255,255,.82)!important;
            text-decoration:none!important;
        }

        .gsm-footer .gsm-footer-v11__column a:hover,
        .gsm-footer .gsm-footer-v11__column a:focus{
            color:#ffffff!important;
            text-decoration:none!important;
            opacity:1!important;
        }

        .gsm-footer .gsm-footer-v11__brand p{
            color:rgba(255,255,255,.82)!important;
        }

        .gsm-footer .gsm-footer-v11-bottom__credit{
            color:rgba(255,255,255,.62)!important;
        }

        .gsm-footer .gsm-footer-v11-bottom__credit strong{
            color:#ffffff!important;
        }

        .gsm-footer .gsm-footer-v11-bottom{
            margin-top:42px!important;
            padding-top:18px!important;
            border-top:1px solid rgba(255,255,255,.12)!important;
        }

        .gsm-footer .gsm-footer-v11-bottom__copyright{
            display:block!important;
            margin:0!important;
            color:rgba(255,255,255,.88)!important;
            font-size:12px!important;
            line-height:1.55!important;
        }

        .gsm-footer .gsm-footer-v11-bottom__credit{
            display:block!important;
            margin-top:4px!important;
            color:rgba(255,255,255,.58)!important;
            font-size:11px!important;
            line-height:1.55!important;
        }

        .gsm-footer .gsm-footer-v11-bottom__credit strong{
            color:rgba(255,255,255,.88)!important;
            font-weight:800!important;
        }

        @media(max-width:900px){
            .gsm-footer .gsm-footer-v11{
                display:grid!important;
                grid-template-columns:1fr 1fr!important;
                gap:34px 44px!important;
            }

            .gsm-footer .gsm-footer-v11__brand,
            .gsm-footer .gsm-footer-v11__column{
                min-width:0!important;
                width:100%!important;
            }
        }

        @media(max-width:600px){
            .gsm-footer .gsm-footer-v11{
                display:flex!important;
                flex-direction:column!important;
                gap:28px!important;
            }

            .gsm-footer .gsm-footer-v11__brand,
            .gsm-footer .gsm-footer-v11__column{
                width:100%!important;
            }

            .gsm-footer .gsm-footer-v11__logo{
                width:175px!important;
                max-width:175px!important;
            }
        }

    </style>
    <?php $this->head() ?>
</head>
<body class="gsm-public" data-public-layout="v22-4">
<?php $this->beginBody() ?>
<header class="gsm-header"><div class="container gsm-nav-wrap">
    <a class="gsm-brand gsm-brand-logo" href="<?= Url::to(['/site/index']) ?>" aria-label="Sumut Mengajar">
        <?php if($logoUrl): ?><img src="<?= Html::encode($logoUrl) ?>" alt="Sumut Mengajar"><?php else: ?><span class="gsm-brand-fallback"><span class="gsm-brand-mark">SM</span><span><b>sumut</b>mengajar<small>Gerakan Sumut Mengajar</small></span></span><?php endif; ?>
    </a>
    <button class="gsm-nav-toggle" type="button" aria-label="Buka menu">☰</button>
    <nav class="gsm-nav">
        <a href="<?= Url::to(['/site/about']) ?>">Tentang</a>
        <a href="<?= Url::to(['/documentation/index']) ?>">Dokumentasi</a>
        <a href="<?= Url::to(['/alumni/index']) ?>">Alumni</a>
        <a href="<?= Url::to(['/article/index']) ?>">Artikel</a>
        <a href="<?= Url::to(['/recruitment/index']) ?>">Rekrutmen</a>
        <a class="nav-track-highlight" href="<?= Url::to(['/site/track']) ?>">
            <span class="nav-track-dot"></span>
            Cek Status Pendaftaran
        </a>

        <?php if(Yii::$app->user->isGuest): ?>
            <a class="nav-login" href="<?= Url::to(['/site/login']) ?>">Masuk</a>
            <a class="nav-cta" href="<?= Url::to(['/site/register']) ?>">Daftar</a>
        <?php else: ?>
            <a class="nav-cta" href="<?= $dashboardUrl ?>"><?= $isStaff?'Dashboard Petugas':'Dashboard' ?></a>
        <?php endif; ?>
    </nav>
</div></header>
<main><?= $content ?></main>
<footer class="gsm-footer">
    <div class="container gsm-footer-v11">

        <div class="gsm-footer-v11__brand">
            <?php if($footerLogoUrl): ?>
                <img
                    class="gsm-footer-v11__logo"
                    src="<?= Html::encode($footerLogoUrl) ?>"
                    alt="Sumut Mengajar"
                >
            <?php else: ?>
                <div class="footer-brand-text">SUMUT MENGAJAR</div>
            <?php endif; ?>

            <p>
                <?= Html::encode($brand['tagline']) ?><br>
                <?= Html::encode($brand['motto']) ?>
            </p>
        </div>

        <div class="gsm-footer-v11__column gsm-footer-v11__nav">
            <b>Navigasi</b>
            <a href="<?= Url::to(['/site/about']) ?>">Tentang Kami</a>
            <a href="<?= Url::to(['/recruitment/index']) ?>">Rekrutmen</a>
            <a href="<?= Url::to(['/site/track']) ?>">Cek Status Pendaftaran</a>
            <a href="<?= Url::to(['/article/index']) ?>">Artikel</a>
            <a href="<?= Url::to(['/alumni/index']) ?>">Alumni</a>
            <a href="<?= Url::to(['/documentation/index']) ?>">Dokumentasi Pengabdian</a>
        </div>

        <div class="gsm-footer-v11__column gsm-footer-v11__alumni">
            <b>Alumni</b>
            <a href="<?= Url::to(['/alumni/index']) ?>">Direktori Alumni</a>
            <a href="<?= Url::to(['/alumni/register']) ?>">Daftarkan Alumni</a>
            <span>Karya &amp; Riset Alumni</span>
        </div>

        <div class="gsm-footer-v11__column gsm-footer-v11__contact">
            <b>Terhubung</b>
            <span><?= Html::encode($brand['instagram']) ?></span>
            <span><?= Html::encode($brand['domain']) ?></span>
            <a href="<?= Url::to(['/site/staff-login']) ?>">Portal Petugas</a>
        </div>

    </div>

    <div class="container footer-bottom gsm-footer-v11-bottom">
        <span class="gsm-footer-v11-bottom__copyright">
            © <?= date('Y') ?> Gerakan Sumut Mengajar. All rights reserved.
        </span>
        <span class="gsm-footer-v11-bottom__credit">
            Website developed &amp; maintained by <strong>Rama Susi</strong>
        </span>
    </div>
</footer>

<?php $this->endBody() ?>
</body></html>
<?php $this->endPage() ?>
