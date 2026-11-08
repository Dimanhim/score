<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Quick-pick income ("доход по умолчанию").
 *
 * @property int $id
 * @property string $name
 * @property int $category
 */
class IncomeTemplate extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%incomes_default}}';
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
