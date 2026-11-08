<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var app\models\Score[] $counted */
/** @var app\models\Score[] $credit */
/** @var app\models\Score[] $hidden */
/** @var int $total */
/** @var int $debt */

use app\helpers\Icon;
use app\helpers\Money;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Счета';

$letter = static fn (string $name): string => Html::encode(mb_strtoupper(mb_substr($name, 0, 1)));
?>
<header class="topbar">
    <h1 class="topbar__title">Счета</h1>
    <a class="icon-btn" href="<?= Url::to(['transfer/create']) ?>" aria-label="Перевод между счетами">
        <?= Icon::svg('arrow-left-right') ?>
    </a>
</header>

<p class="eyebrow">Всего на счетах</p>
<p class="h1" style="margin: 6px 0 4px"><?= Money::html($total) ?></p>
<?php if ($debt > 0): ?>
    <p class="muted">долг по кредиткам <?= Money::plain($debt) ?></p>
<?php endif ?>

<?php if ($counted): ?>
    <section class="group">
        <p class="eyebrow">Учитываются</p>
        <div class="card card--flush">
            <div class="rows rows--card stagger">
                <?php foreach ($counted as $i => $score): ?>
                    <div class="row" style="--i: <?= $i ?>">
                        <span class="row__icon"><?= $letter($score->name) ?></span>
                        <span class="row__main row__title">
                            <?= Html::encode($score->name) ?>
                            <?php if ((int) $score->id_default === 1): ?>
                                <span class="star" title="Счёт по умолчанию"><?= Icon::svg('star', 'icon--sm') ?></span>
                            <?php endif ?>
                        </span>
                        <span class="row__end"><?= Money::html((int) $score->summa) ?></span>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </section>
<?php endif ?>

<?php if ($credit): ?>
    <section class="group">
        <p class="eyebrow">Кредитные карты</p>
        <div class="card card--flush">
            <div class="rows rows--card">
                <?php foreach ($credit as $score): ?>
                    <?php $used = $score->credit_limit > 0 ? min(100, $score->debt / $score->credit_limit * 100) : 0 ?>
                    <div class="row">
                        <span class="row__icon row__icon--credit"><?= Icon::svg('credit-card', 'icon--sm') ?></span>
                        <span class="row__main">
                            <span class="row__title" style="display:block"><?= Html::encode($score->name) ?></span>
                            <span class="row__sub" style="display:block">
                                долг <?= Money::plain($score->debt) ?> из <?= Money::plain((int) $score->credit_limit) ?>
                            </span>
                            <span class="credit__bar" style="display:block"><span style="width: <?= round($used, 1) ?>%"></span></span>
                        </span>
                        <span class="row__end"><?= Money::html((int) $score->summa) ?></span>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </section>
<?php endif ?>

<?php if ($hidden): ?>
    <details class="group">
        <summary class="eyebrow"><?= Icon::svg('chevron-right', 'icon--sm') ?> Скрытые (<?= count($hidden) ?>)</summary>
        <div class="card card--flush">
            <div class="rows rows--card">
                <?php foreach ($hidden as $score): ?>
                    <div class="row">
                        <span class="row__icon"><?= $letter($score->name) ?></span>
                        <span class="row__main row__title"><?= Html::encode($score->name) ?></span>
                        <span class="row__end"><?= Money::html((int) $score->summa) ?></span>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </details>
<?php endif ?>
