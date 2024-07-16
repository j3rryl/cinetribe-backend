<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faction extends Model
{
    use HasFactory;
    protected $fillable = ["name", "media_id", "description", "thumbnail"];

    public function media(){
        return $this->belongsTo(Media::class);
    }

    public function images(){
        return $this->hasMany(FactionImage::class);
    }
}
