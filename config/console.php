<?php
$params = require __DIR__ . '/params.php';
$dbFile = __DIR__ . '/db.php';
$db = file_exists($dbFile) ? require $dbFile : require __DIR__ . '/db.php.example';

return [
    'id' => 'sumut-mengajar-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'language' => 'id-ID',
    'timeZone' => 'Asia/Jakarta',
    'components' => [
        'db' => $db,
        'authManager' => ['class' => 'yii\rbac\DbManager'],
        'log' => [
            'targets' => [[
                'class' => 'yii\log\FileTarget',
                'levels' => ['error', 'warning'],
            ]],
        ],
    ],
    'params' => $params,
];
