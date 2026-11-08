<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\Budget $budget */
/** @var array $recent */
/** @var array $categories */
/** @var array<int, string> $accounts */

use app\helpers\Dates;
use app\helpers\Money;
use yii\helpers\Url;

$this->title = 'Главная';

$progressClass = static fn (float $ratio): string => $ratio > 1 ? ' progress--over' : ($ratio > .8 ? ' progress--warn' : '');
$width = static fn (float $ratio): string => round(min(1, $ratio) * 100, 1) . '%';
$todayRatio = $budget->todayRatio();
$limitRatio = $budget->limitRatio();
$maxCategory = $categories ? max(array_column($categories, 'sum')) : 0;
?>
<header class="topbar">
    <span class="brand">Сальдо</span>
    <span class="period-chip" title="Период бюджета">
        <?= Dates::short($budget->periodBegin) ?> — <?= Dates::short($budget->periodEnd - 1) ?>
    </span>
</header>

<section class="hero">
    <p class="eyebrow">Можно тратить</p>
    <p class="hero__value<?= $budget->available < 0 ? ' is-negative' : '' ?>"><?= Money::html($budget->available) ?></p>
    <p class="hero__meta">
        на счетах <b><?= Money::plain($budget->onAccounts) ?></b>
        <?php if ($budget->debt > 0): ?> · долг по кредиткам <b><?= Money::plain($budget->debt) ?></b><?php endif ?>
        · в резерве <b><?= Money::plain($budget->reserved) ?></b>
    </p>
</section>

<?php if ($budget->limit > 0): ?>
    <a class="card limit" href="<?= Url::to(['operation/index', 'type' => 'cost']) ?>">
        <div class="limit__head">
            <span class="limit__title">Сегодня</span>
            <span class="limit__value">
                <?= Money::html($budget->spentToday) ?>
                <span class="of">из <?= Money::plain($budget->todayAllowance) ?></span>
            </span>
        </div>
        <div class="progress<?= $progressClass($todayRatio) ?>">
            <div class="progress__bar" style="width: <?= $width($todayRatio) ?>"></div>
        </div>
        <p class="limit__foot">
            <?php if ($budget->periodOver): ?>
                Период закончился — обновите его в настройках
            <?php else: ?>
                За период <?= Money::html($budget->spent) ?> из <?= Money::plain($budget->limit) ?>
                · осталось дней: <?= $budget->daysLeft ?>
            <?php endif ?>
        </p>
        <div class="progress<?= $progressClass($limitRatio) ?>">
            <div class="progress__bar" style="width: <?= $width($limitRatio) ?>"></div>
        </div>
    </a>
<?php endif ?>

<section class="section">
    <div class="section__head">
        <h2 class="h2">Последние</h2>
        <a class="link" href="<?= Url::to(['operation/index']) ?>">Все</a>
    </div>
    <?php if ($recent): ?>
        <div class="rows rows--compact stagger">
            <?php foreach ($recent as $i => $row): ?>
                <?= $this->render('//operation/_row', ['row' => $row, 'i' => $i, 'compact' => true, 'accounts' => $accounts]) ?>
            <?php endforeach ?>
        </div>
    <?php else: ?>
        <p class="muted">Операций пока нет — нажмите +, чтобы добавить первую.</p>
    <?php endif ?>
</section>

<?php if ($categories): ?>
    <section class="section">
        <div class="section__head">
            <h2 class="h2">Категории периода</h2>
        </div>
        <ul class="bars">
            <?php foreach ($categories as $cat): ?>
                <li>
                    <span class="bars__name"><?= yii\helpers\Html::encode($cat['name']) ?></span>
                    <span class="bars__sum"><?= Money::html($cat['sum']) ?></span>
                    <span class="bars__track">
                        <span class="bars__fill" style="display:block; width: <?= $maxCategory ? round($cat['sum'] / $maxCategory * 100, 1) : 0 ?>%"></span>
                    </span>
                </li>
            <?php endforeach ?>
        </ul>
    </section>
<?php endif ?>
