<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use app\assets\AppAsset;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag([
    'name' => 'viewport',
    'content' => 'width=device-width, initial-scale=1, viewport-fit=cover',
]);
$this->registerMetaTag(['name' => 'theme-color', 'content' => '#F5F7F2']);
$this->registerMetaTag(['name' => 'apple-mobile-web-app-capable', 'content' => 'yes']);
$this->registerMetaTag(['name' => 'apple-mobile-web-app-status-bar-style', 'content' => 'default']);
$this->registerMetaTag(['name' => 'apple-mobile-web-app-title', 'content' => 'Сальдо']);
$this->registerLinkTag(['rel' => 'preconnect', 'href' => 'https://fonts.googleapis.com']);
$this->registerLinkTag(['rel' => 'preconnect', 'href' => 'https://fonts.gstatic.com', 'crossorigin' => true]);
$this->registerLinkTag(['rel' => 'manifest', 'href' => Yii::getAlias('@web/manifest.webmanifest')]);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => Yii::getAlias('@web/icons/icon-192.png')]);
$this->registerLinkTag(['rel' => 'apple-touch-icon', 'href' => Yii::getAlias('@web/icons/apple-touch-icon.png')]);
