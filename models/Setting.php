<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Single-row settings: budget period [time_begin, time_end), monthly limit and notes.
 *
 * @property int $id
 * @property int $time_begin
 * @property int $time_end
 * @property int|null $month_limit
 * @property string|null $notes_1
 * @property string|null $notes_2
 * @property string|null $notes_3
 * @property string|null $notes_4
 */
class Setting extends ActiveRecord
{
    private static ?self $current = null;

    public static function tableName(): string
    {
        return '{{%settings}}';
    }

    public function rules(): array
    {
        return [
            [['time_begin', 'time_end'], 'required'],
            [['time_begin', 'time_end', 'month_limit'], 'integer'],
            [['notes_1', 'notes_2', 'notes_3', 'notes_4'], 'string'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'time_begin' => 'Начало периода',
            'time_end' => 'Конец периода',
            'month_limit' => 'Лимит расходов на период',
            'notes_1' => 'Общие заметки',
            'notes_2' => 'Заметки по покупкам',
            'notes_3' => 'Заметки по инвестициям',
            'notes_4' => 'Другие заметки',
        ];
    }

    public static function current(): self
    {
        if (self::$current === null) {
            self::$current = static::find()->orderBy(['id' => SORT_ASC])->one() ?? new self([
                'time_begin' => strtotime('first day of this month 00:00'),
                'time_end' => strtotime('first day of next month 00:00'),
                'month_limit' => 0,
            ]);
        }

        return self::$current;
    }
}
