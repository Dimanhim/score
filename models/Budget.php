<?php

declare(strict_types=1);

namespace app\models;

/**
 * Figures for the home screen, computed for the period from settings.
 */
final class Budget
{
    public readonly int $onAccounts;
    public readonly int $debt;
    public readonly int $reserved;
    public readonly int $available;

    public readonly int $periodBegin;
    public readonly int $periodEnd;
    public readonly bool $periodOver;
    public readonly int $limit;
    /** Spent in the period on expenses counted in the daily limit. */
    public readonly int $spent;
    public readonly int $spentToday;
    public readonly int $daysLeft;
    /** What can be spent today so the rest of the period stays within the limit. */
    public readonly int $todayAllowance;

    public function __construct()
    {
        $onAccounts = 0;
        $debt = 0;
        foreach (Score::find()->all() as $score) {
            if ($score->isCredit) {
                $debt += $score->debt;
            } elseif ((int) $score->is_check === 1) {
                $onAccounts += (int) $score->summa;
            }
        }
        $this->onAccounts = $onAccounts;
        $this->debt = $debt;
        $this->reserved = Saving::total() + Payment::total();
        $this->available = $onAccounts - $debt - $this->reserved;

        $settings = Setting::current();
        $this->periodBegin = (int) $settings->time_begin;
        $this->periodEnd = (int) $settings->time_end;
        $this->limit = (int) $settings->month_limit;

        $today = strtotime('today');
        $tomorrow = strtotime('tomorrow');
        $this->periodOver = $today >= $this->periodEnd;

        $limited = Cost::find()->where(['check_for_days' => 1]);
        $this->spent = (int) (clone $limited)
            ->andWhere(['>=', 'date', $this->periodBegin])
            ->andWhere(['<', 'date', $this->periodEnd])
            ->sum('cost');
        $this->spentToday = (int) (clone $limited)
            ->andWhere(['>=', 'date', $today])
            ->andWhere(['<', 'date', $tomorrow])
            ->sum('cost');

        $this->daysLeft = $this->periodOver ? 1 : max(1, (int) ceil(($this->periodEnd - $today) / 86400));
        $leftBeforeToday = $this->limit - ($this->spent - ($this->periodOver ? 0 : $this->spentToday));
        $this->todayAllowance = max(0, (int) floor($leftBeforeToday / $this->daysLeft));
    }

    public function limitRatio(): float
    {
        return $this->limit > 0 ? $this->spent / $this->limit : 0.0;
    }

    public function todayRatio(): float
    {
        return $this->todayAllowance > 0 ? $this->spentToday / $this->todayAllowance : ($this->spentToday > 0 ? 2.0 : 0.0);
    }

    /**
     * Top-level expense categories in the period, largest first.
     *
     * @return array<int, array{id: int, name: string, sum: int}>
     */
    public function topCategories(int $limit = 4): array
    {
        $rows = Cost::find()
            ->select(['category', 's' => 'SUM([[cost]])'])
            ->andWhere(['>=', 'date', $this->periodBegin])
            ->andWhere(['<', 'date', $this->periodEnd])
            ->groupBy('category')
            ->asArray()
            ->all();

        $sums = [];
        foreach ($rows as $row) {
            $root = Category::rootIdOf((int) $row['category']);
            $sums[$root] = ($sums[$root] ?? 0) + (int) $row['s'];
        }
        arsort($sums);

        $result = [];
        foreach (array_slice($sums, 0, $limit, true) as $id => $sum) {
            $result[] = ['id' => $id, 'name' => Category::nameOf($id), 'sum' => $sum];
        }

        return $result;
    }
}
