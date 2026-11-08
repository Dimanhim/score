<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var int $savings */
/** @var int $payments */

use app\helpers\Icon;
use app\helpers\Money;
use yii\helpers\Html;

$this->title = 'Ещё';

$groups = [
    'Аналитика' => [
        ['icon' => 'chart-column', 'label' => 'Статистика'],
        ['icon' => 'calendar-days', 'label' => 'По дням'],
        ['icon' => 'history', 'label' => 'Журнал'],
    ],
    'Справочники' => [
        ['icon' => 'folder', 'label' => 'Категории'],
        ['icon' => 'bookmark', 'label' => 'Шаблоны расходов'],
        ['icon' => 'bookmark', 'label' => 'Шаблоны доходов'],
    ],
    'Настройки' => [
        ['icon' => 'sliders', 'label' => 'Период и лимит'],
        ['icon' => 'pen-line', 'label' => 'Заметки'],
    ],
];
?>
<header class="topbar">
    <h1 class="topbar__title">Ещё</h1>
</header>

<div class="reserves">
    <div class="card">
        <p class="reserve__label">Копилка</p>
        <p class="reserve__value"><?= Money::html($savings) ?></p>
    </div>
    <div class="card">
        <p class="reserve__label">Обязательные</p>
        <p class="reserve__value"><?= Money::html($payments) ?></p>
    </div>
</div>

<nav class="menu">
    <?php foreach ($groups as $title => $items): ?>
        <p class="eyebrow"><?= $title ?></p>
        <div class="rows rows--compact">
            <?php foreach ($items as $item): ?>
                <a class="row menu__item<?= empty($item['url']) ? ' is-soon' : '' ?>" href="<?= empty($item['url']) ? '#' : $item['url'] ?>">
                    <span class="row__icon"><?= Icon::svg($item['icon'], 'icon--sm') ?></span>
                    <span class="row__main row__title"><?= $item['label'] ?></span>
                    <?php if (empty($item['url'])): ?>
                        <span class="menu__badge">скоро</span>
                    <?php else: ?>
                        <?= Icon::svg('chevron-right', 'icon--sm row__chev') ?>
                    <?php endif ?>
                </a>
            <?php endforeach ?>
        </div>
    <?php endforeach ?>

    <p class="eyebrow">Аккаунт</p>
    <div class="rows rows--compact">
        <?= Html::beginForm(['site/logout'], 'post') ?>
            <button class="row menu__item menu__item--danger" type="submit">
                <span class="row__icon"><?= Icon::svg('log-out', 'icon--sm') ?></span>
                <span class="row__main row__title">Выйти</span>
            </button>
        <?= Html::endForm() ?>
    </div>
</nav>
