<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostFloor extends Model
{
    use HasFactory;
    protected $fillable = [ 'owner_user_id', 'post_id', 'content', 'floorNum'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
