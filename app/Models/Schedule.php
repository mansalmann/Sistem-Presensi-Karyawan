<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'user_id',
        'shift_id',
        'office_id',
        'is_banned',
        'work_from_anywhere'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function shift(){
        return $this->belongsTo(Shift::class);
    }
    
    public function office(){
        return $this->belongsTo(Office::class);
    }
}
