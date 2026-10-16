<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqItem extends Model
{
    protected $table = "faq_items";
    protected $primaryKey = "faq_item_id";
    public $timestamps = true;

    protected $fillable = [
        "faq_item_id",
        "faq_id",
        "faq_question",
        "faq_answer",
        "faq_item_order",
        "created_at",
        "updated_at",
    ];

    public function faq()
    {
        return $this->belongsTo(Faq::class, 'faq_id', 'faq_id');
    }
}
