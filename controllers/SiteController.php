<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\Budget;
use app\models\Feed;
use app\models\LoginForm;
use app\models\Payment;
use app\models\Saving;
use app\models\Score;
use Yii;
use yii\web\ErrorAction;
use yii\web\Response;

class SiteController extends BaseController
{
    protected function guestActions(): array
    {
        return ['login', 'error'];
    }

    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
                'layout' => Yii::$app->user->isGuest ? 'auth' : 'main',
            ],
        ];
    }

    public function actionIndex(): string
    {
        $budget = new Budget();

        return $this->render('index', [
            'budget' => $budget,
            'recent' => Feed::page(Feed::ALL, 1, 3)['rows'],
            'categories' => $budget->topCategories(),
            'accounts' => Score::find()->select('name')->indexBy('id')->column(),
        ]);
    }

    public function actionMore(): string
    {
        return $this->render('more', [
            'savings' => Saving::total(),
            'payments' => Payment::total(),
        ]);
    }

    public function actionLogin(): Response|string
    {
        $this->layout = 'auth';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load($this->request->post()) && $model->login()) {
            return $this->goBack();
        }
        $model->password = '';

        return $this->render('login', ['model' => $model]);
    }

    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->redirect(['site/login']);
    }
}
