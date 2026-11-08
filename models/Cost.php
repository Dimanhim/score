<?php

declare(strict_types=1);

namespace app\models;

use app\helpers\Money;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Expense. Saving/deleting keeps the account balance in sync; wrap calls in a transaction.
 *
 * `date` is the day's midnight + 1 second (legacy convention shared with the old app).
 *
 * @property int $id
 * @property string $name
 * @property int $category
 * @property int $category_child
 * @property int|null $costs_default
 * @property int $cost
 * @property int $score
 * @property int|null $check_for_days counted in the daily limit
 * @property int $date
 * @property int $created_at
 * @property int $updated_at
 */
class Cost extends ActiveRecord
{
    public string $dateInput = '';
    public int|string|null $paymentId = null;

    public static function tableName(): string
    {
        return '{{%costs}}';
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
            $this->check_for_days = 1;
            $this->score = Score::defaultId();
        }
    }

    public function rules(): array
    {
        return [
            [['cost', 'score', 'category', 'dateInput'], 'required'],
            [['cost'], 'integer', 'min' => 1, 'max' => 100_000_000],
            [['name'], 'trim'],
            [['name'], 'string', 'max' => 250],
            [['score'], 'exist', 'targetClass' => Score::class, 'targetAttribute' => 'id'],
            [['category'], 'exist', 'targetClass' => Category::class, 'targetAttribute' => 'id'],
            [['paymentId'], 'exist', 'targetClass' => Payment::class, 'targetAttribute' => 'id', 'skipOnEmpty' => true],
            [['costs_default'], 'integer'],
            [['check_for_days'], 'boolean'],
            [['dateInput'], 'date', 'format' => 'php:Y-m-d'],
            [['cost', 'score', 'category', 'check_for_days'], 'filter', 'filter' => 'intval'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'cost' => 'Сумма',
            'name' => 'Название',
            'category' => 'Категория',
            'score' => 'Счёт',
            'dateInput' => 'Дата',
            'check_for_days' => 'Учитывать в дневном лимите',
            'paymentId' => 'Из обязательного платежа',
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
            $this->name = Category::nameOf((int) $this->category);
        }
        $this->date = strtotime($this->dateInput . ' 00:00:00') + 1;
        $this->category_child ??= 0;
        $this->costs_default ??= 0;

        return true;
    }

    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            Score::adjust((int) $this->score, -(int) $this->cost);
            $paymentId = $this->paymentId ? (int) $this->paymentId : null;
            if ($paymentId) {
                Payment::updateAllCounters(['summa' => -(int) $this->cost], ['id' => $paymentId]);
            }
            $account = Score::findOne($this->score)?->name ?? '';
            Log::write(
                Log::TYPE_COST,
                (int) $this->cost,
                $this->name,
                (int) $this->score,
                "Расход «{$this->name}» со счёта {$account} на " . Money::plain((int) $this->cost),
                $paymentId,
            );

            return;
        }

        if (array_key_exists('cost', $changedAttributes) || array_key_exists('score', $changedAttributes)) {
            $oldScore = (int) ($changedAttributes['score'] ?? $this->score);
            $oldCost = (int) ($changedAttributes['cost'] ?? $this->cost);
            Score::adjust($oldScore, $oldCost);
            Score::adjust((int) $this->score, -(int) $this->cost);
        }
    }

    public function afterDelete(): void
    {
        parent::afterDelete();
        Score::adjust((int) $this->score, (int) $this->cost);
    }
}
