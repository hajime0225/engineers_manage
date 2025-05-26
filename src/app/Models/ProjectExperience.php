<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectExperience extends Model
{
    use HasFactory;

    /**
     * マスアサインメント可能な属性。
     *
     * @var array
     */
    protected $fillable = [
        'engineer_id',
        'project_name',
        'industry',
        'roles',
        'phases',
        'project_summary',
        'start_date',
        'end_date',
        'tools_technologies_used',
    ];

    /**
     * この職務経歴を持つエンジニアを取得します。
     */
    public function engineer()
    {
        return $this->belongsTo(Engineer::class);
    }
}
