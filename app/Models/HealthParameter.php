<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthParameter extends Model
{
    use HasFactory;

    protected $table = 'health_parameters';
    protected $primaryKey = 'health_parameter_id';
    public $timestamps = true;

    protected $fillable = [
        'health_parameter_name',
        'health_parameter_question',
        'health_parameter_show_type',
        'health_parameter_option1',
        'health_parameter_option2',
        'health_parameter_option3',
        'health_parameter_option4',
        'health_parameter_order',
        'health_parameter_status',
    ];

    public function scopeActive($query)
    {
        return $query->where('health_parameter_status', '1');
    }

    public function getOptionsArrayAttribute()
    {
        return array_filter([
            $this->health_parameter_option1,
            $this->health_parameter_option2,
            $this->health_parameter_option3,
            $this->health_parameter_option4,
        ]);
    }
}
