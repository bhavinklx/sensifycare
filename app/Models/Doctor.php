<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $table = "doctor";
    protected $primaryKey = "doctor_id";
    public $timestamps = false;

    protected $fillable = [
        "doctor_id",
        "doctor_uid",
        "doctor_fname",
        "doctor_lname",
        "doctor_age",
        "doctor_gender",
        "doctor_email",
        "doctor_phone",
        "doctor_password",
        "doctor_marital_status",
        "doctor_qualification",
        "doctor_designation",
        "doctor_blood_group",
        "doctor_address",
        "doctor_city",
        "doctor_state",
        "doctor_country",
        "doctor_postal_code",
        "doctor_order",
        "doctor_status",
        "created_at"
    ];
}
