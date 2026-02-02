<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bcategory extends Model
{
    protected $table = "bcategory";
    protected $primaryKey = "bcategory_id";
    public $timestamps = false;

    protected $fillable = [
        "bcategory_id",
        "bcategory_title",
        "bcategory_slug",
        "bcategory_meta_title",
        "bcategory_meta_keyword",
        "bcategory_meta_desc",
        "bcategory_order",
        "bcategory_status",
        "created_at"
    ];
}