<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LanguageSkill extends Model
{
    use HasFactory;

    /**
     * マスアサインメント可能な属性。
     *
     * @var array
     */
    protected $fillable = [
        'engineer_id',
        'language_name',
        'proficiency_level',
    ];

    /**
     * この外国語スキルを持つエンジニアを取得します。
     */
    public function engineer()
    {
        return $this->belongsTo(Engineer::class);
    }
}
