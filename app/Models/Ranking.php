<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    use HasFactory;
    protected $fillable = [
        'ranking_code',
        'ranking_name',
        'department_id',
        'table_name',
        'hdc_link',
        'target_value',
        'score_5',
        'score_4',
        'score_3',
        'score_2_5',
        'score_2',
        'score_1',
        'score_1_operator',
        'score_0',
        'rank',
        'weight',
        'score_total',
    ];

    protected $casts = [
        'ranking_code' => 'string',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
}
