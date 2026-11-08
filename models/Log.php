<?php

declare(strict_types=1);

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Activity journal. Transfers have no own table and live here as type 3 rows,
 * one per side: negative value on the source account, positive on the target.
 *
 * @property int $id
 * @property int $type_id
 * @property int $value
 * @property string|null $name
 * @property string|null $desc
 * @property int|null $score_id
 * @property int|null $payment_id
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Log extends ActiveRecord
{
    public const TYPE_COST = 1;
    public const TYPE_INCOME = 2;
    public const TYPE_TRANSFER = 3;

    public static function tableName(): string
    {
        return '{{%logs}}';
    }

    public function behaviors(): array
    {
        return [TimestampBehavior::class];
    }

    public function rules(): array
    {
        return [
            [['type_id', 'value'], 'required'],
            [['type_id', 'value', 'score_id', 'payment_id'], 'integer'],
            [['name', 'desc'], 'string', 'max' => 255],
        ];
    }

    public static function write(int $type, int $value, ?string $name, ?int $scoreId, string $desc, ?int $paymentId = null): void
    {
        $log = new self([
            'type_id' => $type,
            'value' => $value,
            'name' => $name === null ? null : mb_substr($name, 0, 255),
            'score_id' => $scoreId,
            'payment_id' => $paymentId,
            'desc' => mb_substr($desc, 0, 255),
        ]);
        $log->save(false);
    }
}
