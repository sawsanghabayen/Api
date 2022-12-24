<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    
    protected $fillable =['*'];
    protected $casts = [ 'created_at' => 'datetime:Y-m-d H:i'];



    public function user()
{
    return $this->belongsTo(User::class, 'user_id', 'id');
}
    public function chat()
{
    return $this->belongsTo(Chat::class, 'chat_id', 'id');
}

}
