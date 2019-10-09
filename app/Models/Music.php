<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Music extends Model
{
    protected $table = 'musics';
    // protected $fillable = ['cateid', 'name', 'old_price', 'price', 'content', 'tagid', 'is_use', 'sum', 'sort', 'view_num', 'sold_num', 'pic'];
    protected $hidden = [];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
