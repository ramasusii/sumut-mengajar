<?php
$params = require __DIR__ . '/params.php';
$dbFile = __DIR__ . '/db.php';
if (!file_exists($dbFile)) {
    throw new RuntimeException('Pengaturan koneksi belum tersedia.');
}
$db = require $dbFile;

$cookieValidationKey = (string) getenv('APP_COOKIE_VALIDATION_KEY');
if ($cookieValidationKey === '') {
    if (strtolower((string) getenv('YII_ENV')) === 'prod') {
        throw new RuntimeException('Kunci keamanan aplikasi belum tersedia.');
    }
    $cookieValidationKey = 'sumut-mengajar-local-development-key';
}
$secureCookies = filter_var(getenv('APP_COOKIE_SECURE') ?: false, FILTER_VALIDATE_BOOLEAN);

$config = [
    'id' => 'sumut-mengajar',
    'name' => 'Sumut Mengajar',
    'language' => 'id-ID',
    'sourceLanguage' => 'id-ID',
    'timeZone' => 'Asia/Jakarta',
    'basePath' => dirname(__DIR__),
    'defaultRoute' => 'site/index',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'authManager' => ['class' => 'yii\rbac\DbManager'],
        'request' => [
            'cookieValidationKey' => $cookieValidationKey,
            'csrfParam' => '_csrf-sumut-mengajar',
        ],
        'cache' => ['class' => 'yii\caching\FileCache'],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'nullDisplay' => '-',
            'dateFormat' => 'php:d M Y',
            'datetimeFormat' => 'php:d M Y H:i',
            'timeZone' => 'Asia/Jakarta',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['site/staff-login'],
            'identityCookie' => [
                'name' => '_identity-sumut-mengajar',
                'httpOnly' => true,
                'secure' => $secureCookies,
                'sameSite' => yii\web\Cookie::SAME_SITE_LAX,
            ],
            'idParam' => '_id_sumut_mengajar',
        ],
        'errorHandler' => ['errorAction' => 'site/error'],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [[
                'class' => 'yii\log\FileTarget',
                'levels' => ['error', 'warning'],
                'logVars' => [],
            ]],
        ],
        'session' => [
            'class' => 'yii\web\Session',
            'savePath' => '@runtime/sessions',
            'name' => 'sumut-mengajar-session',
            'cookieParams' => [
                'httponly' => true,
                'secure' => $secureCookies,
                'sameSite' => yii\web\Cookie::SAME_SITE_LAX,
                'lifetime' => 0,
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'showScriptName' => false,
            'enablePrettyUrl' => true,
            'rules' => [
                '' => 'site/index',
                'tentang' => 'site/about',
                'alumni' => 'alumni/index',
                'alumni/daftar' => 'alumni/register',
                'alumni/<slug:[a-z0-9\-]+>' => 'alumni/view',
                'artikel' => 'article/index',
                'artikel/<slug:[a-z0-9\-]+>' => 'article/view',
                'cek-status' => 'site/track',
                'tracking' => 'site/track',
                'site/track' => 'site/track',
                'login' => 'site/login',
                'petugas/login' => 'site/staff-login',
                'admin/login' => 'site/staff-login',
                'register' => 'site/register',
                'logout' => 'site/logout',
                'rekrutmen' => 'recruitment/index',
                'rekrutmen/<slug:[a-z0-9\-]+>' => 'recruitment/view',
                'dashboard' => 'applicant/dashboard',
                'pendaftaran/<id:\d+>' => 'applicant/apply',
                'lamaran/<id:\d+>' => 'applicant/application',
                'admin-dashboard' => 'admin-dashboard/index',
                'admin/rekrutmen' => 'admin-recruitment/index',
                'admin/pendaftar' => 'admin-applicant/index',
                'admin/alumni' => 'admin-alumni/index',
                'admin/hero' => 'admin-hero/index',
                'admin/artikel' => 'admin-post/index',
            ],
        ],
    ],
    'params' => $params,
    'modules' => [
        'admin' => [
            'class' => 'mdm\admin\Module',
            'layout' => 'left-menu',
            'mainLayout' => '@app/views/layouts/main.php',
            'menus' => ['user' => null],
        ],
    ],
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            'site/index', 'site/about', 'site/login', 'site/staff-login', 'site/track', 'site/register', 'site/logout', 'site/error',
            'recruitment/index', 'recruitment/view',
            'alumni/index', 'alumni/view', 'alumni/register',
            'article/index', 'article/view',
            // Controller internal ini mengatur role masing-masing dan memakai login portal yang berbeda.
            'applicant/*', 'admin-dashboard/*', 'admin-recruitment/*', 'admin-applicant/*', 'admin-alumni/*', 'admin-hero/*', 'admin-post/*',
        ],
    ],
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
    $config['as access']['allowActions'][] = 'gii/*';
}

return $config;
