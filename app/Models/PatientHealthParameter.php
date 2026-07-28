<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientHealthParameter extends Model
{
    use HasFactory;

    protected $table = 'patient_health_parameter';
    protected $primaryKey = 'patient_health_parameter_id';
    public $timestamps = true;

    protected $fillable = [
        'patient_id',
        'health_parameter_id',
        'health_parameter_answer',
    ];

    public function healthParameter()
    {
        return $this->belongsTo(HealthParameter::class, 'health_parameter_id', 'health_parameter_id');
    }
}
