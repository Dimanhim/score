<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $content */

use yii\helpers\Html;

$this->render('_head');
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

<div class="shell no-nav">
    <main class="screen" id="main">
        <?= $content ?>
    </main>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
