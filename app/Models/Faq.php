<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $table = "faq";
    protected $primaryKey = "faq_id";
    public $timestamps = false;

    protected $fillable = [
        "faq_id",
        "fcategory_id",
        "blog_id",
        "faq_order",
        "faq_status",
        "created_at",
        "updated_at",
    ];

    public function category()
    {
        return $this->belongsTo(Fcategory::class, 'fcategory_id', 'fcategory_id');
    }

    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id', 'blog_id');
    }

    public function items()
    {
        return $this->hasMany(FaqItem::class, 'faq_id', 'faq_id')->orderBy('faq_item_order', 'asc');
    }
}
