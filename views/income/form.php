<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\Income $model */
/** @var string $return */

use app\models\Category;
use app\models\IncomeTemplate;
use app\models\Score;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$isNew = $model->isNewRecord;
$this->title = $isNew ? 'Новый доход' : 'Доход';
$templates = $isNew ? IncomeTemplate::find()->orderBy(['name' => SORT_ASC])->all() : [];
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
    <?= Html::activeHiddenInput($model, 'income_default', ['data-field' => 'template']) ?>

    <?= $this->render('//_form/_amount', [
        'form' => $form, 'model' => $model, 'attribute' => 'income', 'kind' => 'income', 'autofocus' => $isNew,
    ]) ?>

    <?php if ($templates): ?>
        <div class="field">
            <div class="chips">
                <?php foreach ($templates as $tpl): ?>
                    <button class="chip<?= (int) $model->income_default === (int) $tpl->id ? ' is-active' : '' ?>" type="button"
                            data-template="<?= $tpl->id ?>"
                            data-name="<?= Html::encode($tpl->name) ?>"
                            data-category="<?= (int) $tpl->category ?>"><?= Html::encode($tpl->name) ?></button>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <?= $form->field($model, 'name')->textInput([
        'placeholder' => 'Если пусто — по категории',
        'autocomplete' => 'off',
        'data-field' => 'name',
    ]) ?>

    <?= $form->field($model, 'category')->dropDownList(Category::pickerGroups(Category::INCOME), [
        'prompt' => 'Без категории',
        'data-field' => 'category',
    ]) ?>

    <?= $this->render('//_form/_account', [
        'form' => $form, 'model' => $model, 'attribute' => 'score', 'accounts' => Score::forPicker(),
    ]) ?>

    <?= $this->render('//_form/_date', ['model' => $model, 'attribute' => 'dateInput']) ?>
</div>

<div class="form__actions">
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn--primary btn--block', 'data-primary' => true]) ?>
    <?php if (!$isNew): ?>
        <?= Html::a('Удалить', ['income/delete', 'id' => $model->id], [
            'class' => 'btn btn--danger btn--block',
            'data-method' => 'post',
            'data-confirm' => 'Удалить доход? Сумма спишется со счёта.',
            'data-params' => ['return' => $return],
        ]) ?>
    <?php endif ?>
</div>
<?php ActiveForm::end() ?>
