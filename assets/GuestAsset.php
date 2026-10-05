<?php
namespace app\assets;

use yii\web\AssetBundle;

class GuestAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'web/assets-guest/css/sumut-mengajar.css',
        'web/assets-guest/css/portal-v2.css',
        'web/assets-guest/css/brand-home-patch.css',
        'web/assets-guest/css/launch-v3.css',
    ];

    public $js = [
        'web/assets-guest/js/sumut-mengajar.js',
        'web/assets-guest/js/launch-v3.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
