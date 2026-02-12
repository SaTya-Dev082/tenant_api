<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class RoomPhoto extends Model
{
    protected $fillable = ['room_id', 'photos_path', 'is_main'];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
