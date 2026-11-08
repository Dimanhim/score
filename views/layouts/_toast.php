<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;

$toast = Yii::$app->session->getFlash('toast');
if (!is_array($toast) || empty($toast['text'])) {
    return;
}
?>
<div class="toast" role="status" data-toast>
    <span class="toast__text"><?= Html::encode($toast['text']) ?></span>
    <?php if (!empty($toast['undo'])): ?>
        <?= Html::beginForm($toast['undo'], 'post') ?>
            <button class="toast__action" type="submit">Отменить</button>
        <?= Html::endForm() ?>
    <?php endif ?>
</div>
