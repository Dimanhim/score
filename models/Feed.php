<?php

declare(strict_types=1);

namespace app\models;

use yii\db\Expression;
use yii\db\Query;

/**
 * Unified operations list: expenses, incomes and transfers (outgoing side from the journal).
 */
final class Feed
{
    public const ALL = 'all';
    public const COST = 'cost';
    public const INCOME = 'income';

    public const TYPES = [self::ALL, self::COST, self::INCOME];

    /**
     * @return array{rows: array<int, array<string, mixed>>, hasMore: bool}
     */
    public static function page(string $type, int $page = 1, int $perPage = 40): array
    {
        $rows = (new Query())
            ->from(['u' => self::union($type)])
            ->orderBy(['date' => SORT_DESC, 'created_at' => SORT_DESC, 'id' => SORT_DESC])
            ->offset(($page - 1) * $perPage)
            ->limit($perPage + 1)
            ->all();

        $hasMore = count($rows) > $perPage;

        return ['rows' => array_slice($rows, 0, $perPage), 'hasMore' => $hasMore];
    }

    /**
     * Rows grouped by calendar day, newest first, with the day's expense and income totals.
     *
     * @return array<string, array{ts: int, rows: array, cost: int, income: int}>
     */
    public static function groupByDay(array $rows): array
    {
        $days = [];
        foreach ($rows as $row) {
            $key = date('Y-m-d', (int) $row['date']);
            $days[$key] ??= ['ts' => strtotime($key), 'rows' => [], 'cost' => 0, 'income' => 0];
            $days[$key]['rows'][] = $row;
        }
        if ($days === []) {
            return [];
        }

        $from = min(array_column($days, 'ts'));
        $to = max(array_column($days, 'ts')) + 86400;
        foreach ([[Cost::class, 'cost', 'cost'], [Income::class, 'income', 'income']] as [$class, $column, $bucket]) {
            $amounts = $class::find()
                ->select(['date', 'a' => $column])
                ->where(['>=', 'date', $from])
                ->andWhere(['<', 'date', $to])
                ->asArray()
                ->all();
            foreach ($amounts as $item) {
                $key = date('Y-m-d', (int) $item['date']);
                if (isset($days[$key])) {
                    $days[$key][$bucket] += (int) $item['a'];
                }
            }
        }

        return $days;
    }

    private static function union(string $type): Query
    {
        $costs = (new Query())
            ->select([
                'type' => new Expression("'cost'"),
                'id', 'name', 'amount' => 'cost', 'category', 'score', 'date', 'created_at',
            ])
            ->from(Cost::tableName());

        $incomes = (new Query())
            ->select([
                'type' => new Expression("'income'"),
                'id', 'name', 'amount' => 'income', 'category', 'score', 'date', 'created_at',
            ])
            ->from(Income::tableName());

        $transfers = (new Query())
            ->select([
                'type' => new Expression("'transfer'"),
                'id', 'name', 'amount' => new Expression('-[[value]]'), 'category' => new Expression('NULL'),
                'score' => 'score_id', 'date' => 'created_at', 'created_at',
            ])
            ->from(Log::tableName())
            ->where(['type_id' => Log::TYPE_TRANSFER])
            ->andWhere(['<', 'value', 0]);

        return match ($type) {
            self::COST => $costs,
            self::INCOME => $incomes,
            default => $costs->union($incomes, true)->union($transfers, true),
        };
    }
}
