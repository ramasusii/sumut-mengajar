<?php
// Dependensi AdminLTE/yii2-admin lama masih memanggil beberapa fungsi PHP
// dengan nilai null. Pada PHP 8.1+ hal itu berupa E_DEPRECATED dan, saat
// YII_DEBUG aktif, dapat berubah menjadi halaman error. Error aplikasi yang
// sebenarnya tetap ditampilkan; hanya notifikasi kompatibilitas vendor lama
// yang tidak dijadikan exception.
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED & ~E_USER_DEPRECATED);
// Aman secara default: pengguna tidak melihat stack trace dan lokasi file server.
// Untuk diagnosis lokal sementara jalankan dengan YII_DEBUG=1 YII_ENV=dev.
$debugFlag = getenv('YII_DEBUG');
$environment = getenv('YII_ENV');
defined('YII_DEBUG') or define('YII_DEBUG', $debugFlag !== false && filter_var($debugFlag, FILTER_VALIDATE_BOOLEAN));
defined('YII_ENV') or define('YII_ENV', $environment !== false && $environment !== '' ? $environment : 'prod');

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/config/web.php';

(new yii\web\Application($config))->run();
