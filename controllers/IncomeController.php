<?php

declare(strict_types=1);

namespace app\controllers;

use app\helpers\Money;
use app\models\Income;
use app\models\IncomeTemplate;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class IncomeController extends BaseController
{
    public function actionCreate(?int $template = null): Response|string
    {
        $model = new Income();
        if ($template !== null && ($tpl = IncomeTemplate::findOne($template))) {
            $model->name = $tpl->name;
            $model->category = (int) $tpl->category;
            $model->income_default = (int) $tpl->id;
        }

        if ($model->load($this->request->post()) && $model->validate()) {
            $this->saveInTransaction($model);
            $this->toast('Доход ' . Money::plain((int) $model->income) . ' добавлен', ['income/delete', 'id' => $model->id]);

            return $this->redirectBack(['site/index']);
        }

        return $this->render('form', ['model' => $model, 'return' => $this->returnPath()]);
    }

    public function actionUpdate(int $id): Response|string
    {
        $model = $this->find($id);

        if ($model->load($this->request->post()) && $model->validate()) {
            $this->saveInTransaction($model);
            $this->toast('Доход сохранён');

            return $this->redirectBack();
        }

        return $this->render('form', ['model' => $model, 'return' => $this->returnPath()]);
    }

    public function actionDelete(int $id): Response
    {
        $model = $this->find($id);
        $tx = Yii::$app->db->beginTransaction();
        try {
            $model->delete();
            $tx->commit();
        } catch (\Throwable $e) {
            $tx->rollBack();
            throw $e;
        }
        $this->toast('Доход удалён, ' . Money::plain((int) $model->income) . ' списаны со счёта');

        return $this->redirectBack();
    }

    private function saveInTransaction(Income $model): void
    {
        $tx = Yii::$app->db->beginTransaction();
        try {
            $model->save(false);
            $tx->commit();
        } catch (\Throwable $e) {
            $tx->rollBack();
            throw $e;
        }
    }

    private function find(int $id): Income
    {
        return Income::findOne($id) ?? throw new NotFoundHttpException('Доход не найден');
    }
}
