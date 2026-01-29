<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pages extends Model
{
    protected $table = "pages";
    protected $primaryKey = "page_id";
    public $timestamps = false;

    protected $fillable = [
        "page_id",
        "page_parent",
        "page_title",
        "page_slug",
        "page_link",
        "page_image",
        "page_desc",
        "page_meta_title",
        "page_meta_keyword",
        "page_meta_desc",
        "page_order",
        "page_status",
        "page_header_status",
        "page_footer_status",
        "created_at"
    ];

    public function subPages()
    {
        return $this->hasMany(self::class, 'page_parent')->orderBy('page_order');
    }
}
