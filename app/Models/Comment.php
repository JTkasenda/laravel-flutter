<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //
    protected $fillable = ['body', 'user_id', 'feed_id'];

    public function feed(){
        return $this->belongsTo(Feed::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
