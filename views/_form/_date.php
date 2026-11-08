<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\base\Model $model */
/** @var string $attribute holds Y-m-d */

use app\helpers\Icon;
use yii\helpers\Html;

$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('yesterday'));
?>
<div class="field">
    <span class="field__label"><?= Html::encode($model->getAttributeLabel($attribute)) ?></span>
    <div class="date-pick" data-date-pick>
        <button class="chip" type="button" data-date="<?= $today ?>">Сегодня</button>
        <button class="chip" type="button" data-date="<?= $yesterday ?>">Вчера</button>
        <label class="chip date-pick__other" data-date-other>
            <?= Icon::svg('calendar-days', 'icon--sm') ?>
            <span>Другая дата</span>
            <?= Html::activeInput('date', $model, $attribute, ['max' => $today, 'aria-label' => 'Выбрать дату']) ?>
        </label>
    </div>
    <?= Html::error($model, $attribute, ['class' => 'field__error']) ?>
</div>
