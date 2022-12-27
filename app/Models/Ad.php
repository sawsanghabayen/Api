<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    protected $hidden = ['user_id','created_at' ,'updated_at'];
    protected $appends = ['store_name','store_image' ,'subcategories_count','type_ad' ,'image_url', 'is_favorite', 'ad_reviews'];
    protected $casts = ['active'=>'boolean'];

    
    public function getImageUrlAttribute()
    {
        if ($this->images()->count() > 0) {
            return url('storage/' . $this->images()->first()->url);
        }
        return null;
    }
    public function getAdReviewsAttribute()
    {
        if (auth('user-api')->check()) {
            return $this->reviewAds()->get();
        }
        return 0;
    }

    public function getIsFavoriteAttribute()
    {
        if (auth('user-api')->check()) {
            return $this->favoriters()->where('user_id', auth('user-api')->id())->exists();
        }
        return false;
    }

    public function getTypeAdAttribute()
    {
        return $this->type ='N' ? 'New' : 'Used';
    }

    public function getStoreNameAttribute()
    {
        return $this->user()->first()->name;
    }

    public function getStoreImageAttribute()
    {
        return $this->user()->first()->image;
    }

    // public function getSubcategoryAdsNameAttribute()
    // {
    //     return $this->subcategoryAds()->subcategory->name;
    // }



    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'object', 'object_type', 'object_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    
    public function favoriters()
    {
        return $this->hasMany(FavoriteAd::class, 'ad_id', 'id');
    }
    

    public function reviewAds()
    {
        return $this->hasMany(Review::class, 'ad_id', 'id');
    }

    public function subcategoryAds()
    {
        return $this->hasMany(SubcategoryAd::class, 'ads_id', 'id');
    }

    
    public function getSubcategoriesCountAttribute()
    {
        return $this->subcategoryAds()->count();
    }


}
