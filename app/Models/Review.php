<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $appends = ['store_name','store_image'];
    protected $casts = [ 'created_at' => 'datetime:Y-m-d'];

    
    public function getStoreImageAttribute()
    {
        return $this->user()->first()->image;
    }

    public function getStoreNameAttribute()
    {
        return $this->user()->first()->name;
    }

    public function ad()
    {
        return $this->belongsTo(Ad::class, 'ad_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
