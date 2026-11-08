<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Piggy bank ("копилка"). Its sum is subtracted from available money.
 *
 * @property int $id
 * @property string|null $name
 * @property int $summa
 */
class Saving extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%save}}';
    }

    public function rules(): array
    {
        return [
            [['name'], 'string', 'max' => 255],
            [['summa'], 'integer'],
            [['summa'], 'default', 'value' => 0],
        ];
    }

    public static function total(): int
    {
        return (int) static::find()->sum('summa');
    }
}
