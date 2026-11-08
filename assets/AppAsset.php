<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://fonts.googleapis.com/css2?family=Onest:wght@400;500;600&family=Unbounded:wght@600;700&display=swap',
        'css/tokens.css',
        'css/app.css',
    ];
    public $js = [
        'js/app.js',
    ];
    public $jsOptions = [
        'defer' => true,
    ];
    public $depends = [
        YiiAsset::class,
    ];
}
