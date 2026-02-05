<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = "banner";
    protected $primaryKey = "banner_id";
    public $timestamps = false;

    protected $fillable = [
        "banner_id",
        "banner_title",
        "banner_image",
        "banner_text",
        "banner_text1",
        "banner_order",
        "banner_status",
        "created_at"
    ];
}