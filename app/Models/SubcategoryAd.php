<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubcategoryAd extends Model
{
    use HasFactory;

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcateggory_id', 'id');
    }
}
