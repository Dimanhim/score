<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\widgets\ActiveForm $form */
/** @var yii\base\Model $model */
/** @var string $attribute */
/** @var app\models\Score[] $accounts */
/** @var string|null $label */
/** @var string|null $dataField */

use app\helpers\Money;

$counted = [];
$other = [];
$options = [];
foreach ($accounts as $account) {
    if ((int) $account->is_check === 1) {
        $counted[$account->id] = $account->name;
    } else {
        $other[$account->id] = $account->name;
    }
    $options[$account->id] = [
        'data-balance' => $account->isCredit
            ? Money::plain((int) $account->summa) . ' доступно, долг ' . Money::plain($account->debt)
            : Money::plain((int) $account->summa),
    ];
}
$items = $other ? ['Основные' => $counted, 'Скрытые' => $other] : $counted;
$hintId = 'hint-' . $attribute;

$field = $form->field($model, $attribute, [
    'template' => "{label}\n{input}\n<p class=\"field__hint\" id=\"{$hintId}\"></p>\n{error}",
]);
if (!empty($label)) {
    $field->label($label);
}

echo $field->dropDownList($items, [
    'prompt' => 'Выберите счёт',
    'options' => $options,
    'data-balance-hint' => $hintId,
    'data-field' => $dataField ?? null,
]);
