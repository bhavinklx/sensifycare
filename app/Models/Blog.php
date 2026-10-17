<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = "blog";
    protected $primaryKey = "blog_id";
    public $timestamps = false;

    protected $fillable = [
        "blog_id",
        "bcategory_id",
        "bsubcategory_id",
        "blog_type",
        "blog_title",
        "blog_slug",
        "blog_date",
        "blog_image",
        "blog_video_url",
        "blog_short_desc",
        "blog_desc",
        "blog_meta_title",
        "blog_meta_keyword",
        "blog_meta_desc",
        "blog_canonical",
        "blog_order",
        "blog_status",
        "created_at"
    ];

    public function category()
    {
        return $this->belongsTo(Bcategory::class, 'bcategory_id', 'bcategory_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(Bcategory::class, 'bsubcategory_id', 'bcategory_id');
    }

    public function faq()
    {
        return $this->hasOne(Faq::class, 'blog_id', 'blog_id');
    }
}
