<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'groups';
    // protected $fillable = ['name', 'price', 'content', 'tagid', 'is_use', 'sum', 'sort', 'view_num', 'sold_num', 'pic'];
    protected $hidden = [];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

}
