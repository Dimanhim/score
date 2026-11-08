<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\Category;
use app\models\Feed;
use app\models\Score;

class OperationController extends BaseController
{
    public function actionIndex(string $type = Feed::ALL, int $page = 1): string
    {
        if (!in_array($type, Feed::TYPES, true)) {
            $type = Feed::ALL;
        }
        $page = max(1, $page);
        $result = Feed::page($type, $page);

        return $this->render('index', [
            'type' => $type,
            'page' => $page,
            'days' => Feed::groupByDay($result['rows']),
            'hasMore' => $result['hasMore'],
            'accounts' => Score::find()->select('name')->indexBy('id')->column(),
            'categories' => Category::all(),
        ]);
    }
}
