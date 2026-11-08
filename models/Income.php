<?php

declare(strict_types=1);

namespace app\models;

use app\helpers\Money;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Income. Saving/deleting keeps the account balance in sync; wrap calls in a transaction.
 *
 * @property int $id
 * @property string $name
 * @property int $income_default
 * @property int $income
 * @property int $date
 * @property int|null $category
 * @property int $score
 * @property int $created_at
 * @property int $updated_at
 */
class Income extends ActiveRecord
{
    public string $dateInput = '';

    public static function tableName(): string
    {
        return '{{%incomes}}';
    }

    public function behaviors(): array
    {
        return [TimestampBehavior::class];
    }

    public function init(): void
    {
        parent::init();
        if ($this->isNewRecord) {
            $this->dateInput = date('Y-m-d');
            $this->score = Score::defaultId();
        }
    }

    public function rules(): array
    {
        return [
            [['income', 'score', 'dateInput'], 'required'],
            [['income'], 'integer', 'min' => 1, 'max' => 100_000_000],
            [['name'], 'trim'],
            [['name'], 'string', 'max' => 250],
            [['score'], 'exist', 'targetClass' => Score::class, 'targetAttribute' => 'id'],
            [['category'], 'exist', 'targetClass' => Category::class, 'targetAttribute' => 'id', 'skipOnEmpty' => true],
            [['income_default'], 'integer'],
            [['dateInput'], 'date', 'format' => 'php:Y-m-d'],
            [['income', 'score'], 'filter', 'filter' => 'intval'],
            [['category'], 'filter', 'filter' => static fn ($v) => $v === '' || $v === null ? null : (int) $v],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'income' => 'Сумма',
            'name' => 'Название',
            'category' => 'Категория',
            'score' => 'Счёт',
            'dateInput' => 'Дата',
        ];
    }

    public function afterFind(): void
    {
        parent::afterFind();
        $this->dateInput = date('Y-m-d', (int) $this->date);
    }

    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($this->name === null || $this->name === '') {
            $this->name = $this->category ? Category::nameOf((int) $this->category) : 'Доход';
        }
        $this->date = strtotime($this->dateInput . ' 00:00:00') + 1;
        $this->income_default ??= 0;

        return true;
    }

    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            Score::adjust((int) $this->score, (int) $this->income);
            $account = Score::findOne($this->score)?->name ?? '';
            Log::write(
                Log::TYPE_INCOME,
                (int) $this->income,
                $this->name,
                (int) $this->score,
                "Доход «{$this->name}» на счёт {$account} на " . Money::plain((int) $this->income),
            );

            return;
        }

        if (array_key_exists('income', $changedAttributes) || array_key_exists('score', $changedAttributes)) {
            $oldScore = (int) ($changedAttributes['score'] ?? $this->score);
            $oldIncome = (int) ($changedAttributes['income'] ?? $this->income);
            Score::adjust($oldScore, -$oldIncome);
            Score::adjust((int) $this->score, (int) $this->income);
        }
    }

    public function afterDelete(): void
    {
        parent::afterDelete();
        Score::adjust((int) $this->score, -(int) $this->income);
    }
}
