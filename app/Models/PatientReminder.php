<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientReminder extends Model
{
    protected $table = 'patient_reminders';
    protected $primaryKey = 'patient_reminder_id';
    
    protected $fillable = [
        'patient_id',
        'title',
        'date_text',
        'label',
        'icon_type',
        'type',
        'is_completed'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }
}
