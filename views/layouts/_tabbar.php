<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use app\helpers\Icon;
use yii\helpers\Url;

$route = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
$tabs = [
    ['label' => 'Главная', 'icon' => 'house', 'url' => ['site/index'], 'active' => $route === 'site/index'],
    ['label' => 'Операции', 'icon' => 'list', 'url' => ['operation/index'], 'active' => Yii::$app->controller->id === 'operation'],
    null,
    ['label' => 'Счета', 'icon' => 'wallet', 'url' => ['account/index'], 'active' => Yii::$app->controller->id === 'account'],
    ['label' => 'Ещё', 'icon' => 'ellipsis', 'url' => ['site/more'], 'active' => $route === 'site/more'],
];
?>
<nav class="tabbar" aria-label="Разделы">
    <?php foreach ($tabs as $tab): ?>
        <?php if ($tab === null): ?>
            <div class="tabbar__fab-slot">
                <button class="fab" type="button" data-fab
                        data-quick-url="<?= Url::to(['cost/create']) ?>"
                        aria-label="Добавить операцию" aria-expanded="false" aria-controls="add-sheet">
                    <?= Icon::svg('plus', 'icon--lg') ?>
                </button>
            </div>
        <?php else: ?>
            <a class="tabbar__item<?= $tab['active'] ? ' is-active' : '' ?>" href="<?= Url::to($tab['url']) ?>"
                <?= $tab['active'] ? 'aria-current="page"' : '' ?>>
                <?= Icon::svg($tab['icon']) ?>
                <span class="tabbar__label"><?= $tab['label'] ?></span>
            </a>
        <?php endif ?>
    <?php endforeach ?>
</nav>
