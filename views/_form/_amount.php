<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\widgets\ActiveForm $form */
/** @var yii\base\Model $model */
/** @var string $attribute */
/** @var string $kind expense|income|transfer */
/** @var bool $autofocus */

echo $form->field($model, $attribute, [
    'options' => ['class' => 'amount-field amount-field--' . $kind],
    'labelOptions' => ['class' => 'visually-hidden'],
    'template' => "{label}\n<div class=\"amount-field__wrap\">{input}<span class=\"amount-field__cur\">₽</span></div>\n{error}",
])->textInput([
    'class' => 'amount-field__input',
    'inputmode' => 'numeric',
    'pattern' => '[0-9]*',
    'placeholder' => '0',
    'autocomplete' => 'off',
    'enterkeyhint' => 'done',
    'data-amount-input' => true,
    'autofocus' => $autofocus,
]);
