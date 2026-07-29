<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Patient extends Authenticatable
{
    use HasApiTokens, Notifiable;

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
        "symptoms",
    ];

    protected $casts = [
        'symptoms' => 'array',
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
        return $this->belongsToMany(Symptom::class, 'patient_symptom', 'patient_id', 'symptom_id');
    }

    public function healthParameters()
    {
        return $this->hasMany(PatientHealthParameter::class, 'patient_id', 'patient_id');
    }
}
