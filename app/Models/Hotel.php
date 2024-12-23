<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;
    protected $table = 'hotels';

    protected $fillable = ['name', 'address', 'city', 'nit', 'total_rooms', 'user_id'];

    public function hotelRooms()
    {
        return $this->hasMany(HotelRoom::class);
    }
}
