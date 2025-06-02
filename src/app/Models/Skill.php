<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'type'];

    public function engineers()
    {
        return $this->belongsToMany(Engineer::class, 'engineer_skills')
                    ->withPivot('experience_years', 'proficiency_level')
                    ->withTimestamps();
    }
}
