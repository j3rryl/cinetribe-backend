<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;
    protected $fillable = ["name", "genre_id", "description", "thumbnail", "media_type"];

    public function factions(){
        return $this->hasMany(Faction::class);
    }

    public function genre(){
        return $this->belongsTo(Genre::class);
    }

    public function images(){
        return $this->hasMany(MediaImage::class);
    }
}
