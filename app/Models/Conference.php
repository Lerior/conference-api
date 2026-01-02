<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conference extends Model
{
    use HasFactory;
    
    protected $fillable = ['title','description','user_id','date'];

    protected $hidden = [ 'created_at','updated_at'];

    public function user () {return $this->belongsTo(User::class);}

    public function attendances () {return $this->hasMany(Attendance::class);}
    public function topics () {return $this->hasMany(Topic::class);}

    public function attendees () {return $this->belongsToMany(User::class, 'attendances');}

    public function scopeTitle ($query, $title){
        return $query->where('title','like', "%{$title}%"); 
    }

    public function scopeBetweenDates($query, $from, $to){
        return $query->whereBetween('date', [$from,$to]);
    }
}
