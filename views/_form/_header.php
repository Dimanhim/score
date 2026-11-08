<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $title */
/** @var string $return */

use app\helpers\Icon;
use yii\helpers\Url;

$this->params['hideNav'] = true;
?>
<header class="topbar topbar--form">
    <a class="icon-btn" href="<?= $return !== '' ? $return : Url::home() ?>" aria-label="Назад"><?= Icon::svg('arrow-left') ?></a>
    <h1 class="topbar__title"><?= $title ?></h1>
</header>
