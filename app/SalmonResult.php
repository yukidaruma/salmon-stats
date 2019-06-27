<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalmonResult extends Model
{
    protected $table = 'salmon_results';
    protected $hidden = [];
    protected $casts = [
        'members' => 'array',
    ];
    protected $guarded = [];

    public function playerResults()
    {
        return $this
            ->hasMany('App\SalmonPlayerResult', 'salmon_id')
            ->with(['bossEliminations', 'specialUses', 'weapons']);
    }

    public function schedule()
    {
        return $this
            ->hasOne('App\SalmonSchedule', 'schedule_id', 'schedule_id');
    }

    public function waves()
    {
        return $this
            ->hasMany('App\SalmonWave', 'salmon_id')
            ->with(['event', 'water'])
            ->orderBy('wave');
    }
}