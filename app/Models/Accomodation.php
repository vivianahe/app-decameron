<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accomodation extends Model
{
    use HasFactory;
    protected $table = 'accommodations';

    protected $fillable = ['room_type_id', 'accommodation'];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function hotelRooms()
    {
        return $this->hasMany(HotelRoom::class);
    }
}
