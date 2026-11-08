<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Obligatory payment reserve. Its sum is subtracted from available money.
 *
 * @property int $id
 * @property string|null $name
 * @property int $summa
 */
class Payment extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%payments}}';
    }

    public function rules(): array
    {
        return [
            [['name'], 'string', 'max' => 250],
            [['summa'], 'integer'],
            [['summa'], 'default', 'value' => 0],
        ];
    }

    public static function total(): int
    {
        return (int) static::find()->sum('summa');
    }
}
