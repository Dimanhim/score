<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Account. For credit cards `summa` is the remaining limit, so debt = credit_limit - summa.
 *
 * @property int $id
 * @property int $is_check counted in available money
 * @property string $name
 * @property string $description
 * @property int $id_default
 * @property int $summa
 * @property int|null $credit_limit
 *
 * @property-read bool $isCredit
 * @property-read int $debt
 */
class Score extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%scores}}';
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 250],
            [['description'], 'string'],
            [['description'], 'default', 'value' => ''],
            [['is_check', 'id_default'], 'boolean'],
            [['summa', 'credit_limit'], 'integer'],
            [['summa', 'is_check', 'id_default'], 'default', 'value' => 0],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Название',
            'description' => 'Описание',
            'id_default' => 'Счёт по умолчанию',
            'is_check' => 'Учитывать в доступных деньгах',
            'summa' => 'Баланс',
            'credit_limit' => 'Кредитный лимит',
        ];
    }

    public function getIsCredit(): bool
    {
        return (int) $this->credit_limit > 0;
    }

    public function getDebt(): int
    {
        return $this->isCredit ? max(0, (int) $this->credit_limit - (int) $this->summa) : 0;
    }

    /**
     * Atomic balance change, safe against concurrent writes from the old app.
     */
    public static function adjust(int $id, int $delta): void
    {
        if ($delta !== 0) {
            static::updateAllCounters(['summa' => $delta], ['id' => $id]);
        }
    }

    /**
     * Accounts for pickers: counted ones first, default on top.
     *
     * @return self[]
     */
    public static function forPicker(): array
    {
        return static::find()
            ->orderBy(['is_check' => SORT_DESC, 'id_default' => SORT_DESC, 'name' => SORT_ASC])
            ->all();
    }

    public static function defaultId(): ?int
    {
        $id = static::find()->select('id')->where(['id_default' => 1])->scalar();

        return $id === false || $id === null ? null : (int) $id;
    }
}
