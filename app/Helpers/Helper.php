<?php

namespace App\Helpers;

use Carbon\Carbon;

class Helper
{
    public static function convertGradePoint(Array $job): int
    {
        return ($job['grade']['id'] - 1) * 100 + $job['grade_point'];
    }

    public static function generateApiToken()
    {
        $token = random_bytes(60);

        return hash('sha256', $token);
    }

    public static function makeIdTokeyMap ($rows)
    {
        $result = new \stdClass();
        foreach ($rows as $row) {
            $result->{$row->id} = $row->key;
        }
        return $result;
    }

    public static function mapCount($associativeArray)
    {
        return array_map(function($item) { return $item['count']; }, $associativeArray);
    }

    /**
     * @throws InvalidArgumentException if the $schedule_id is in invalid format
     */
    public static function scheduleIdToTimestamp($scheduleId)
    {
        if (is_null($scheduleId)) return null;

        return Carbon::createFromFormat('YmdH', $scheduleId)->format('Y-m-d H:i:s');
    }
}
