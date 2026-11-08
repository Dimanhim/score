<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $content */

use yii\helpers\Html;

$this->render('_head');
$hideNav = !empty($this->params['hideNav']);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?php $this->head() ?>
    <title><?= Html::encode($this->title ? $this->title . ' · Сальдо' : 'Сальдо') ?></title>
</head>
<body>
<?php $this->beginBody() ?>
<?= $this->render('_icons') ?>

<div class="shell<?= $hideNav ? ' no-nav' : '' ?>" data-shell>
    <main class="screen<?= $hideNav ? ' screen--form' : '' ?>" id="main">
        <?= $content ?>
    </main>

    <?php if (!$hideNav): ?>
        <?= $this->render('_tabbar') ?>
        <?= $this->render('_add-sheet') ?>
    <?php endif ?>

    <?= $this->render('_toast') ?>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
