<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fcategory extends Model
{
    protected $table = "fcategory";
    protected $primaryKey = "fcategory_id";
    public $timestamps = false;

    protected $fillable = [
        "fcategory_id",
        "fcategory_title",
        "fcategory_slug",
        "fcategory_order",
        "fcategory_status",
        "created_at",
        "updated_at",
    ];

    public function faqs()
    {
        return $this->hasMany(Faq::class, 'fcategory_id', 'fcategory_id')->orderBy('faq_order', 'ASC');
    }
}
