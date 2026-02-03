<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $table = "patient";
    protected $primaryKey = "patient_id";
    public $timestamps = false;

    protected $fillable = [
        "patient_id",
        "patient_uid",
        "patient_fname",
        "patient_lname",
        "patient_image",
        "patient_age",
        "patient_gender",
        "patient_email",
        "patient_phone",
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
        "created_at"
    ];
}
