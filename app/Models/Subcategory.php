<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    public function subcategoryAds()
    {
        return $this->hasMany(SubcategoryAd::class, 'subcategories_id', 'id');
    }
}
