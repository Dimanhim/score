<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Ошибка';
$code = $exception instanceof yii\web\HttpException ? $exception->statusCode : 500;
?>
<div class="error-page empty">
    <p class="hero__value"><?= $code ?></p>
    <p class="empty__title"><?= $code === 404 ? 'Такой страницы нет' : 'Что-то пошло не так' ?></p>
    <p class="empty__text"><?= Html::encode($message) ?></p>
    <a class="btn btn--secondary" href="<?= Url::home() ?>">На главную</a>
</div>
