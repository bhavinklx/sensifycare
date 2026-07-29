<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Symptom extends Model
{
    protected $table = "symptom";
    protected $primaryKey = "symptom_id";
    public $timestamps = true;

    protected $fillable = [
        "symptom_id",
        "symptom_name",
        "symptom_desc",
        "symptom_image",
        "symptom_emoji",
        "symptom_order",
        "symptom_status",
        "created_at",
        "updated_at"
    ];

    /**
     * The patients that belong to the symptom.
     */
    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'patient_symptom', 'symptom_id', 'patient_id');
    }
}
