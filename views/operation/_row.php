<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var array $row type, id, name, amount, category, score, date */
/** @var int $i position, drives the stagger delay */
/** @var bool $compact */
/** @var array<int, string> $accounts account names by id */

use app\helpers\Icon;
use app\helpers\Money;
use app\models\Category;
use yii\helpers\Html;
use yii\helpers\Url;

$type = $row['type'];
$account = $accounts[(int) $row['score']] ?? 'Счёт удалён';
$style = $i < 8 ? ' style="--i: ' . $i . '"' : '';

if ($type === 'transfer') {
    $title = $row['name'] ?: 'Перевод';
    $sub = $account;
    $icon = '<span class="row__icon row__icon--transfer">' . Icon::svg('arrow-left-right', 'icon--sm') . '</span>';
    $amount = Money::html((int) $row['amount'], Money::TRANSFER);
    $url = null;
} else {
    $category = $row['category'] !== null ? Category::nameOf((int) $row['category']) : 'Без категории';
    $title = $row['name'] ?: $category;
    $sub = $category . ' · ' . $account;
    $letter = mb_strtoupper(mb_substr($category, 0, 1));
    $icon = '<span class="row__icon' . ($type === 'income' ? ' row__icon--income' : '') . '">' . Html::encode($letter) . '</span>';
    $amount = Money::html((int) $row['amount'], $type === 'income' ? Money::INCOME : Money::EXPENSE);
    $url = Url::to([$type . '/update', 'id' => $row['id']]);
}

$tag = $url ? 'a' : 'div';
?>
<<?= $tag ?> class="row"<?= $url ? ' href="' . $url . '"' : '' ?><?= $style ?>>
    <?= $icon ?>
    <span class="row__main">
        <span class="row__title" style="display:block"><?= Html::encode($title) ?></span>
        <span class="row__sub" style="display:block"><?= Html::encode($sub) ?></span>
    </span>
    <span class="row__end"><?= $amount ?></span>
</<?= $tag ?>>
