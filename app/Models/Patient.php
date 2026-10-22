<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $table = "patient";
    protected $primaryKey = "patient_id";
    public $timestamps = true;

    protected $fillable = [
        "patient_uid",
        "patient_fname",
        "patient_lname",
        "patient_image",
        "patient_age",
        "patient_gender",
        "patient_email",
        "patient_phone",
        "patient_dob",
        "patient_password",
        "patient_marital_status",
        "patient_occupation",
        "patient_blood_group",
        "patient_blood_pressure",
        "patient_sugar_level",
        "patient_address",
        "patient_city",
        "patient_state",
        "patient_postal_code",
        "patient_order",
        "patient_status",
        "patient_otp",
        "patient_other_symptoms",
        "preferred_language",
        "height_cm",
        "weight_kg",
        "profile_step",
        "is_profile_complete",
    ];

    protected $casts = [
    ];

    protected $hidden = [
        'patient_password',
        'remember_token',
    ];

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->patient_password;
    }

    public function symptoms()
    {
        return $this->belongsToMany(Symptom::class, 'patient_symptom', 'patient_id', 'symptom_id')->withTimestamps();
    }

    public function getOtherSymptomsAttribute()
    {
        return $this->attributes['patient_other_symptoms'] ?? '';
    }

    public function setOtherSymptomsAttribute($value)
    {
        $this->attributes['patient_other_symptoms'] = $value;
    }

    public function healthParameters()
    {
        return $this->hasMany(PatientHealthParameter::class, 'patient_id', 'patient_id');
    }

    public function reminders()
    {
        return $this->hasMany(PatientReminder::class, 'patient_id', 'patient_id');
    }

    public function notificationSetting()
    {
        return $this->hasOne(PatientNotificationSetting::class, 'patient_id', 'patient_id');
    }

    public function reports()
    {
        return $this->hasMany(PatientReport::class, 'patient_id', 'patient_id')->orderBy('created_at', 'desc');
    }

    public function notifications()
    {
        return $this->hasMany(PatientNotification::class, 'patient_id', 'patient_id')->orderBy('created_at', 'desc');
    }

    public function getFullNameAttribute()
    {
        return trim(($this->patient_fname ?? '') . ' ' . ($this->patient_lname ?? ''));
    }

    public function getBmiAttribute()
    {
        if ($this->height_cm > 0 && $this->weight_kg > 0) {
            $height_m = $this->height_cm / 100;
            return round($this->weight_kg / ($height_m * $height_m), 1);
        }
        return null;
    }

    public function getBmiStatusAttribute()
    {
        $bmi = $this->bmi;
        if (!$bmi) {
            return ['label' => 'Not Calculated', 'badge' => 'bg-secondary-subtle text-secondary', 'class' => 'secondary'];
        }
        if ($bmi < 18.5) {
            return ['label' => 'Underweight', 'badge' => 'bg-info-subtle text-info', 'class' => 'info'];
        }
        if ($bmi <= 24.9) {
            return ['label' => 'Normal Weight', 'badge' => 'bg-success-subtle text-success', 'class' => 'success'];
        }
        if ($bmi <= 29.9) {
            return ['label' => 'Overweight', 'badge' => 'bg-warning-subtle text-warning', 'class' => 'warning'];
        }
        return ['label' => 'Obese', 'badge' => 'bg-danger-subtle text-danger', 'class' => 'danger'];
    }

    public function getImageUrlAttribute()
    {
        if (!empty($this->patient_image) && file_exists(public_path('uploads/patient/' . $this->patient_image))) {
            return asset('uploads/patient/' . $this->patient_image);
        }
        return null;
    }
}
