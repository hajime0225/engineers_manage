<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;
    // タイムスタンプが不要な場合や、他の設定があれば記述

    public function engineers()
    {
        return $this->belongsToMany(Engineer::class, 'engineer_skills')
                    ->withPivot('experience_years', 'proficiency_level')
                    ->withTimestamps();
    }
}
