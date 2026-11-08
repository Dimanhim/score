<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int|null $parent
 * @property string $name
 * @property string $description
 * @property int $source 0 = expense, 1 = income
 * @property int $date
 * @property int|null $show_default
 */
class Category extends ActiveRecord
{
    public const COST = 0;
    public const INCOME = 1;

    /** @var array<int, self>|null */
    private static ?array $all = null;

    public static function tableName(): string
    {
        return '{{%categories}}';
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 250],
            [['description'], 'string', 'max' => 255],
            [['description'], 'default', 'value' => ''],
            [['parent', 'source', 'show_default'], 'integer'],
            [['date'], 'default', 'value' => static fn () => time()],
        ];
    }

    /**
     * All categories indexed by id, loaded once per request.
     *
     * @return array<int, self>
     */
    public static function all(): array
    {
        return self::$all ??= static::find()->indexBy('id')->all();
    }

    public static function nameOf(?int $id): string
    {
        return $id !== null && isset(self::all()[$id]) ? self::all()[$id]->name : 'Без категории';
    }

    public static function rootIdOf(int $id): int
    {
        $all = self::all();
        $guard = 0;
        while (isset($all[$id]) && $all[$id]->parent && isset($all[$all[$id]->parent]) && $guard++ < 10) {
            $id = (int) $all[$id]->parent;
        }

        return $id;
    }

    /**
     * Visible categories of a source as [parentName => [id => name]] with the parent itself first.
     */
    public static function pickerGroups(int $source): array
    {
        $roots = [];
        $children = [];
        foreach (self::all() as $cat) {
            if ((int) $cat->source !== $source || (int) $cat->show_default !== 1) {
                continue;
            }
            if ($cat->parent) {
                $children[(int) $cat->parent][(int) $cat->id] = $cat->name;
            } else {
                $roots[(int) $cat->id] = $cat->name;
            }
        }
        asort($roots);

        $groups = [];
        foreach ($roots as $id => $name) {
            $items = [$id => $name];
            if (isset($children[$id])) {
                asort($children[$id]);
                $items += $children[$id];
            }
            $groups[$name] = $items;
        }

        return $groups;
    }
}
