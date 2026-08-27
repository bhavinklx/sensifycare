<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientNotification extends Model
{
    use HasFactory;

    protected $table = 'patient_notifications';
    protected $primaryKey = 'patient_notification_id';

    protected $fillable = [
        'patient_id',
        'title',
        'body',
        'data',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'data' => 'array',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }
}
