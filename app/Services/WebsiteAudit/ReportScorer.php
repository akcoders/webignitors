<?php

namespace App\Services\WebsiteAudit;

class ReportScorer
{
    /**
     * @param  array<string, mixed>  $analysis
     * @param  array<string, mixed>|null  $mobile
     * @param  array<string, mixed>|null  $desktop
     * @param  array<string, mixed>|null  $w3c
     * @param  array<string, mixed>|null  $observatory
     * @return array<string, int>
     */
    public function score(
        array $analysis,
        ?array $mobile,
        ?array $desktop,
        ?array $w3c,
        ?array $observatory
    ): array {
        $local = $analysis['scores'] ?? [];

        $performance = $this->average([
            data_get($mobile, 'scores.performance'),
            data_get($desktop, 'scores.performance'),
        ], 0);

        $accessibility = $this->average([
            data_get($mobile, 'scores.accessibility'),
            data_get($desktop, 'scores.accessibility'),
        ], 70);

        $seo = $this->weightedAverage([
            [(int) ($local['seo'] ?? 70), 35],
            [$this->average([
                data_get($mobile, 'scores.seo'),
                data_get($desktop, 'scores.seo'),
            ], 70), 65],
        ]);

        $technical = $this->weightedAverage([
            [$this->average([
                data_get($mobile, 'scores.best_practices'),
                data_get($desktop, 'scores.best_practices'),
            ], 70), 65],
            [(int) ($w3c['score'] ?? 70), 35],
        ]);

        $security = $this->weightedAverage([
            [(int) ($local['security'] ?? 60), 45],
            [(int) ($observatory['score'] ?? $local['security'] ?? 60), 55],
        ]);

        $scores = [
            'performance' => $performance,
            'accessibility' => $accessibility,
            'seo' => $seo,
            'technical' => $technical,
            'code' => (int) ($local['code'] ?? 70),
            'design' => (int) ($local['design'] ?? 70),
            'marketing' => (int) ($local['marketing'] ?? 70),
            'security' => $security,
        ];

        $scores['overall'] = $this->weightedAverage([
            [$scores['performance'], 20],
            [$scores['accessibility'], 13],
            [$scores['seo'], 16],
            [$scores['technical'], 12],
            [$scores['code'], 11],
            [$scores['design'], 12],
            [$scores['marketing'], 10],
            [$scores['security'], 6],
        ]);

        return $scores;
    }

    /**
     * @param  array<int, mixed>  $values
     */
    private function average(array $values, int $fallback): int
    {
        $available = array_values(array_filter($values, fn ($value) => is_numeric($value)));

        return $available === []
            ? $fallback
            : (int) round(array_sum($available) / count($available));
    }

    /**
     * @param  array<int, array{0: int, 1: int}>  $values
     */
    private function weightedAverage(array $values): int
    {
        $total = 0;
        $weight = 0;
        foreach ($values as [$value, $itemWeight]) {
            $total += $value * $itemWeight;
            $weight += $itemWeight;
        }

        return max(0, min(100, (int) round($total / max(1, $weight))));
    }
}
