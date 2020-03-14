<?php

namespace App\UseCases;

use App\SalmonResult;
use App\SalmonPlayerResult;

class IndexResultUsecase
{
    public function __invoke($playerId = null, $scheduleTimestamp = null, String $routeName, array $query = [])
    {
        $results = new SalmonResult();

        if (preg_match('/\.summary$/', $routeName)) {
            $perPage = 10;
        } else {
            $perPage = 20;
        }

        if (!is_null($playerId)) {
            $results = new SalmonPlayerResult();

            $results = $results
                ->with(['weapons'])
                ->select(
                    '*',
                    'salmon_results.boss_elimination_count as boss_elimination_count',
                    'salmon_player_results.boss_elimination_count as player_boss_elimination_count',
                )
                ->where('player_id', $playerId);

            if (!is_null($scheduleTimestamp)) {
                $results = $results->where('schedule_id', $scheduleTimestamp);
            }

            $results = $results
                ->join('salmon_results', 'salmon_results.id', '=', 'salmon_player_results.salmon_id')
                ->orderBy('salmon_results.start_at', 'desc');
        } else {
            if (!is_null($scheduleTimestamp)) {
                $results = $results->where('schedule_id', $scheduleTimestamp);
            }

            $results = $results->orderBy('id', 'desc');
        }

        function buildWhere($column, $operator)
        {
            return fn ($results, $value) => $results->where($column, $operator, $value);
        }

        function buildMin($column)
        {
            return buildWhere($column, '>=');
        }

        function buildMax($column)
        {
            return buildWhere($column, '<=');
        }

        $filters = [
            'is_cleared' => function($results, $value) {
                $operator = $value === 'true' ? '=' : '<';
                return $results->where('clear_waves', $operator, 3);
            },
            'min_golden_egg' => buildMin('golden_egg_delivered'),
            'max_golden_egg' => buildMax('golden_egg_delivered'),
            'min_power_egg' => buildMin('power_egg_collected'),
            'max_power_egg' => buildMax('power_egg_collected'),
            'stages' => fn ($results, $value) => $results
                ->join('salmon_schedules', 'salmon_schedules.schedule_id', '=', 'salmon_results.schedule_id')
                ->whereIn('stage_id', explode(',', $value)),
        ];

        foreach ($filters as $key => $filter) {
            if (isset($query[$key])) {
                $results = $filter($results, $query[$key]);
            }
        }

        return $results->paginate($perPage);
    }
}
