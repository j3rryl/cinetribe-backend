<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FactionImage extends Model
{
    use HasFactory;
    protected $fillable = ["thumbnail", "faction_id"];

    public function faction(){
        return $this->belongsTo(Faction::class);
    }
}
