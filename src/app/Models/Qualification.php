<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    use HasFactory;

    /**
     * マスアサインメント可能な属性。
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * この資格を持つエンジニアを取得します。
     */
    public function engineers()
    {
        return $this->belongsToMany(Engineer::class, 'engineer_qualifications')
                    ->withPivot('acquisition_date', 'attempts')
                    ->withTimestamps();
    }
}
