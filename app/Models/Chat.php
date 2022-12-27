<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $fillable =['*'];
    protected $casts = [ 'created_at' => 'datetime:Y-m-d'];


public function sender()
{
    return $this->belongsTo(User::class, 'sender_id', 'id');
}

public function recipient()
{
    return $this->belongsTo(User::class, 'recipient_id', 'id');
}

public function messages()
{
    return $this->hasMany(Message::class, 'chat_id', 'id')->latest();
}


}
