<?php

namespace Database\Seeders;

use App\Models\Accomodation;
use App\Models\RoomType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roomTypes = [
            ['type' => 'Estándar', 'accommodations' => ['Sencilla', 'Doble']],
            ['type' => 'Junior', 'accommodations' => ['Triple', 'Cuádruple']],
            ['type' => 'Suite', 'accommodations' => ['Sencilla', 'Doble', 'Triple']],
        ];

        foreach ($roomTypes as $roomType) {
            // Crear el tipo de habitación
            $createdRoomType = RoomType::create(['type' => $roomType['type']]);

            // Insertar las acomodaciones según el tipo de habitación
            foreach ($roomType['accommodations'] as $accommodationType) {
                Accomodation::create([
                    'room_type_id' => $createdRoomType->id, // Asociamos el tipo de habitación
                    'accommodation' => $accommodationType,  // Inserta en la columna 'accommodation'
                ]);
            }
        }

    }
}
