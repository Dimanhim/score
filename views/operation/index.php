<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $type */
/** @var int $page */
/** @var array $days */
/** @var bool $hasMore */
/** @var array<int, string> $accounts */

use app\helpers\Dates;
use app\helpers\Icon;
use app\helpers\Money;
use app\models\Feed;
use yii\helpers\Url;

$this->title = 'Операции';
$tabs = [Feed::ALL => 'Все', Feed::COST => 'Расходы', Feed::INCOME => 'Доходы'];
$i = 0;
?>
<header class="topbar">
    <h1 class="topbar__title">Операции</h1>
</header>

<nav class="segment" aria-label="Тип операций">
    <?php foreach ($tabs as $key => $label): ?>
        <a class="segment__item<?= $key === $type ? ' is-active' : '' ?>"
           href="<?= Url::to(['operation/index', 'type' => $key === Feed::ALL ? null : $key]) ?>"
            <?= $key === $type ? 'aria-current="page"' : '' ?>><?= $label ?></a>
    <?php endforeach ?>
</nav>

<?php if (!$days): ?>
    <div class="empty">
        <div class="empty__icon"><?= Icon::svg('list') ?></div>
        <p class="empty__title">Операций пока нет</p>
        <p class="empty__text">Нажмите +, чтобы добавить первую трату</p>
        <a class="btn btn--secondary" href="<?= Url::to(['cost/create']) ?>">Добавить расход</a>
    </div>
<?php else: ?>
    <?php foreach ($days as $day): ?>
        <div class="day">
            <span class="eyebrow"><?= Dates::day($day['ts']) ?></span>
            <?php if ($type === Feed::INCOME): ?>
                <?= $day['income'] ? Money::html($day['income'], Money::INCOME) : '' ?>
            <?php else: ?>
                <?= $day['cost'] ? Money::html($day['cost'], Money::EXPENSE) : '' ?>
            <?php endif ?>
        </div>
        <div class="rows stagger">
            <?php foreach ($day['rows'] as $row): ?>
                <?= $this->render('_row', ['row' => $row, 'i' => $i++, 'compact' => false, 'accounts' => $accounts]) ?>
            <?php endforeach ?>
        </div>
    <?php endforeach ?>

    <?php if ($hasMore): ?>
        <div style="padding: 24px 0 8px">
            <a class="btn btn--secondary btn--block"
               href="<?= Url::to(['operation/index', 'type' => $type === Feed::ALL ? null : $type, 'page' => $page + 1]) ?>">
                Показать ещё
            </a>
        </div>
    <?php endif ?>
<?php endif ?>
