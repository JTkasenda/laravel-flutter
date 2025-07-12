<?php

namespace App\Models;

use App\Models\Feed;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    //
    use HasFactory;
    protected $fillable = ['body', 'user_id', 'feed_id'];

    public function feed(){
        return $this->belongsTo(Feed::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
