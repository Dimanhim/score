<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

/**
 * Every screen requires a signed-in user unless the controller opens actions in guestActions().
 */
abstract class BaseController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'actions' => $this->guestActions()],
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['delete' => ['post'], 'logout' => ['post']],
            ],
        ];
    }

    /**
     * @return string[]
     */
    protected function guestActions(): array
    {
        return [];
    }

    /**
     * Redirect to the "return" form field if it is a local path, otherwise to the fallback.
     */
    protected function redirectBack(array|string $fallback = ['operation/index']): Response
    {
        $return = (string) Yii::$app->request->post('return', '');
        if ($return !== '' && str_starts_with($return, '/') && !str_starts_with($return, '//')) {
            return $this->redirect($return);
        }

        return $this->redirect($fallback);
    }

    /**
     * Local path of the page the user came from, used as the form's "return" value.
     */
    protected function returnPath(): string
    {
        $referrer = Yii::$app->request->referrer;
        if ($referrer === null) {
            return '';
        }
        $parts = parse_url($referrer);
        if (($parts['host'] ?? null) !== Yii::$app->request->hostName) {
            return '';
        }
        $path = ($parts['path'] ?? '/') . (isset($parts['query']) ? '?' . $parts['query'] : '');

        return $path === Yii::$app->request->url ? '' : $path;
    }

    protected function toast(string $text, ?array $undoRoute = null): void
    {
        Yii::$app->session->setFlash('toast', ['text' => $text, 'undo' => $undoRoute]);
    }
}
