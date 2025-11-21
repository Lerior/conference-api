<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conference extends Model
{
    use HasFactory;
    
    protected $fillable = ['name','description','user_id','date'];

    public function user () {return $this->belongsTo(User::class);}

    public function attendances () {return $this->hasMany(Attendance::class);}
    public function topics () {return $this->hasMany(Topic::class);}

    public function attendees () {return $this->belongsToMany(User::class, 'attendances');}
}
