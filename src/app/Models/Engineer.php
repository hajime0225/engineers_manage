<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Engineer extends Model
{
    use HasFactory;

    // created_at, updated_at 以外のカラムも必要に応じて $fillable や $guarded を設定

    public function skills()
    {
        // engineersテーブルとskillsテーブルを中間テーブルengineer_skillsを介して多対多の関係で結びつける
        // withPivotで中間テーブルのカラムも取得できるようにする
        return $this->belongsToMany(Skill::class, 'engineer_skills')
                    ->withPivot('experience_years', 'proficiency_level')
                    ->withTimestamps();
    }

    public function projectExperiences()
    {
        // engineersテーブルとproject_experiencesテーブルを1対多の関係で結びつける
        return $this->hasMany(ProjectExperience::class);
    }

    public function qualifications()
    {
        return $this->belongsToMany(Qualification::class, 'engineer_qualifications')
                    ->withPivot('acquisition_date', 'attempts')
                    ->withTimestamps();
    }

    public function languageSkills()
    {
        return $this->hasMany(LanguageSkill::class);
    }
}

