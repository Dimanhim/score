<?php

declare(strict_types=1);

namespace app\models;

use app\helpers\Money;
use Yii;
use yii\base\Model;

class TransferForm extends Model
{
    public int|string|null $from = null;
    public int|string|null $to = null;
    public int|string|null $amount = null;

    public function rules(): array
    {
        return [
            [['from', 'to', 'amount'], 'required'],
            [['amount'], 'integer', 'min' => 1, 'max' => 100_000_000],
            [['from', 'to'], 'exist', 'targetClass' => Score::class, 'targetAttribute' => 'id'],
            [['to'], 'compare', 'compareAttribute' => 'from', 'operator' => '!=', 'message' => 'Выберите другой счёт'],
            [['from', 'to', 'amount'], 'filter', 'filter' => 'intval'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'from' => 'Откуда',
            'to' => 'Куда',
            'amount' => 'Сумма',
        ];
    }

    public function execute(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $from = Score::findOne($this->from);
        $to = Score::findOne($this->to);
        $amount = (int) $this->amount;
        $sum = Money::plain($amount);

        $tx = Yii::$app->db->beginTransaction();
        try {
            Score::adjust($from->id, -$amount);
            Score::adjust($to->id, $amount);
            Log::write(Log::TYPE_TRANSFER, -$amount, "Перевод → {$to->name}", $from->id, "Перевод {$sum}: {$from->name} → {$to->name}");
            Log::write(Log::TYPE_TRANSFER, $amount, "Перевод ← {$from->name}", $to->id, "Перевод {$sum}: {$from->name} → {$to->name}");
            $tx->commit();
        } catch (\Throwable $e) {
            $tx->rollBack();
            throw $e;
        }

        return true;
    }
}
