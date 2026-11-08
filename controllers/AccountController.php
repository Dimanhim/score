<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\Score;

class AccountController extends BaseController
{
    public function actionIndex(): string
    {
        $counted = [];
        $credit = [];
        $hidden = [];
        foreach (Score::find()->orderBy(['id_default' => SORT_DESC, 'summa' => SORT_DESC])->all() as $score) {
            if ($score->isCredit && ((int) $score->is_check === 1 || $score->debt > 0)) {
                $credit[] = $score;
            } elseif (!$score->isCredit && (int) $score->is_check === 1) {
                $counted[] = $score;
            } else {
                $hidden[] = $score;
            }
        }

        return $this->render('index', [
            'counted' => $counted,
            'credit' => $credit,
            'hidden' => $hidden,
            'total' => array_sum(array_map(static fn (Score $s) => (int) $s->summa, $counted)),
            'debt' => array_sum(array_map(static fn (Score $s) => $s->debt, $credit)),
        ]);
    }
}
