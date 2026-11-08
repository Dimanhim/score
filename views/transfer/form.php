<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\TransferForm $model */
/** @var app\models\Score[] $accounts */
/** @var string $return */

use app\helpers\Icon;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Перевод';
?>
<?= $this->render('//_form/_header', ['title' => $this->title, 'return' => $return]) ?>

<?php $form = ActiveForm::begin([
    'options' => ['class' => 'form', 'data-requires-amount' => true],
    'fieldConfig' => [
        'options' => ['class' => 'field'],
        'labelOptions' => ['class' => 'field__label'],
        'inputOptions' => ['class' => 'field__control'],
        'errorOptions' => ['class' => 'field__error'],
    ],
]) ?>
<div class="form__body">
    <?= Html::hiddenInput('return', $return) ?>

    <?= $this->render('//_form/_amount', [
        'form' => $form, 'model' => $model, 'attribute' => 'amount', 'kind' => 'transfer', 'autofocus' => true,
    ]) ?>

    <div class="pair">
        <?= $this->render('//_form/_account', [
            'form' => $form, 'model' => $model, 'attribute' => 'from', 'accounts' => $accounts, 'dataField' => 'from',
        ]) ?>
        <button class="pair__swap" type="button" data-swap aria-label="Поменять счета местами">
            <?= Icon::svg('arrow-up-down', 'icon--sm') ?>
        </button>
        <?= $this->render('//_form/_account', [
            'form' => $form, 'model' => $model, 'attribute' => 'to', 'accounts' => $accounts, 'dataField' => 'to',
        ]) ?>
    </div>
</div>

<div class="form__actions">
    <?= Html::submitButton('Перевести', ['class' => 'btn btn--primary btn--block', 'data-primary' => true]) ?>
</div>
<?php ActiveForm::end() ?>
