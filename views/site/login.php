<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\LoginForm $model */

use app\helpers\Icon;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Вход';
?>
<div class="auth">
    <div class="auth__brand">
        <h1 class="auth__name">Сальдо</h1>
        <p class="auth__tagline">Деньги под контролем</p>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'fieldConfig' => [
            'options' => ['class' => 'field'],
            'labelOptions' => ['class' => 'field__label'],
            'inputOptions' => ['class' => 'field__control'],
            'errorOptions' => ['class' => 'field__error'],
        ],
    ]) ?>

        <?= $form->field($model, 'username')->textInput([
            'autofocus' => true,
            'autocomplete' => 'username',
            'inputmode' => 'email',
            'autocapitalize' => 'off',
        ]) ?>

        <?= $form->field($model, 'password', [
            'template' => "{label}\n<div class=\"password\">{input}"
                . '<button class="icon-btn" type="button" data-toggle-password="loginform-password" aria-label="Показать пароль">'
                . Icon::svg('eye', 'icon--sm') . "</button></div>\n{error}",
        ])->passwordInput(['autocomplete' => 'current-password']) ?>

        <label class="switch">
            <span class="switch__text">Запомнить меня</span>
            <?= Html::activeHiddenInput($model, 'rememberMe', ['value' => 0, 'id' => null]) ?>
            <input type="checkbox" name="<?= Html::getInputName($model, 'rememberMe') ?>" value="1" <?= $model->rememberMe ? 'checked' : '' ?>>
            <span class="switch__track"></span>
        </label>

        <?= Html::submitButton('Войти', ['class' => 'btn btn--primary btn--block', 'name' => 'login-button']) ?>

    <?php ActiveForm::end() ?>
</div>
