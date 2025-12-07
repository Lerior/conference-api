<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Attendance extends Model
{
    use HasFactory;
    protected $fillable = ['conference_id','user_id'];

    protected $hidden = [ 'created_at','updated_at'];

    public function user (){return $this->belongsTo(User::class);}
    public function conference (){return $this->belongsTo(Conference::class);}
}
