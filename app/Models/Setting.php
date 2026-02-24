<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $table = "setting";
    protected $primaryKey = "setting_id";
    public $timestamps = false;

    protected $fillable = [
        "setting_id",
        "setting_label",
        "setting_name",
        "setting_value",
        "setting_type",
        "setting_order",
        "setting_status",
        "setting_for"
    ];
}
