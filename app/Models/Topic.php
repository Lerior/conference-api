<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description', 'conference_id', 'user_id','speaker_name'];

    public function conference () { return $this->belongsTo(Conference::class);}
    
    public function user () { return $this->belongsTo(User::class);}
}
