<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $hidden = ['created_at','updated_at'];

    protected $casts = [
        'status' => 'boolean',
        // 'created_at' => 'datetime:Y-m-d H:ia'
    ];

    public function ads()
    {
        return $this->hasMany(Ad::class, 'category_id', 'id');
    }


    public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'category_id', 'id');
    }
}
