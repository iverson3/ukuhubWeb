<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activities';
    // protected $fillable = ['cateid', 'name', 'old_price', 'price', 'content', 'tagid', 'is_use', 'sum', 'sort', 'view_num', 'sold_num', 'pic'];
    protected $hidden = [];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function setStartTimeAttribute($value)
    {
        $this->attributes['start_time'] = is_int($value) ? $value : strtotime($value);
    }
 
    public function getStartTimeAttribute()
    {
    	if (is_int($this->attributes['start_time']) || strpos($this->attributes['start_time'], '-') === false) {
    		return date('Y-m-d H:i:s', intval($this->attributes['start_time']));
    	}
        return $this->attributes['start_time'];
    }

    public function setEndTimeAttribute($value)
    {
        $this->attributes['end_time'] = is_int($value) ? $value : strtotime($value);
    }
 
    public function getEndTimeAttribute()
    {
    	if (is_int($this->attributes['end_time']) || strpos($this->attributes['end_time'], '-') === false) {
    		return date('Y-m-d H:i:s', intval($this->attributes['end_time']));
    	}
        return $this->attributes['end_time'];
    }

}
