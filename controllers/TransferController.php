<?php

declare(strict_types=1);

namespace app\controllers;

use app\helpers\Money;
use app\models\Score;
use app\models\TransferForm;
use yii\web\Response;

class TransferController extends BaseController
{
    public function actionCreate(): Response|string
    {
        $model = new TransferForm(['from' => Score::defaultId()]);

        if ($model->load($this->request->post()) && $model->execute()) {
            $this->toast('Перевод ' . Money::plain((int) $model->amount) . ' выполнен');

            return $this->redirectBack(['account/index']);
        }

        return $this->render('form', [
            'model' => $model,
            'accounts' => Score::forPicker(),
            'return' => $this->returnPath(),
        ]);
    }
}
