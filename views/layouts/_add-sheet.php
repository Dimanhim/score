<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use app\helpers\Icon;
use app\models\CostTemplate;
use yii\helpers\Html;
use yii\helpers\Url;

$templates = CostTemplate::find()->orderBy(['name' => SORT_ASC])->all();
?>
<div class="sheet-backdrop" data-sheet-close></div>
<section class="sheet" id="add-sheet" role="dialog" aria-modal="true" aria-labelledby="add-sheet-title" aria-hidden="true" inert>
    <div class="sheet__handle"></div>
    <h2 class="sheet__title" id="add-sheet-title">Добавить</h2>

    <div class="tiles">
        <a class="tile tile--expense" href="<?= Url::to(['cost/create']) ?>">
            <span class="tile__icon"><?= Icon::svg('minus') ?></span>Расход
        </a>
        <a class="tile tile--income" href="<?= Url::to(['income/create']) ?>">
            <span class="tile__icon"><?= Icon::svg('plus') ?></span>Доход
        </a>
        <a class="tile tile--transfer" href="<?= Url::to(['transfer/create']) ?>">
            <span class="tile__icon"><?= Icon::svg('arrow-left-right') ?></span>Перевод
        </a>
    </div>

    <?php if ($templates): ?>
        <p class="eyebrow" style="margin: 24px 0 10px">Частые</p>
        <div class="chips">
            <?php foreach ($templates as $tpl): ?>
                <a class="chip" href="<?= Url::to(['cost/create', 'template' => $tpl->id]) ?>"><?= Html::encode($tpl->name) ?></a>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</section>
