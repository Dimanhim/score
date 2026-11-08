<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Quick-pick expense ("расход по умолчанию").
 *
 * @property int $id
 * @property int $category
 * @property string $name
 */
class CostTemplate extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%costs_default}}';
    }

    public function rules(): array
    {
        return [
            [['name', 'category'], 'required'],
            [['name'], 'string', 'max' => 250],
            [['category'], 'integer'],
        ];
    }
}
