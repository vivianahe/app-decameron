<?php

namespace App\Http\Controllers;

use App\Models\Accomodation;
use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HotelRoomController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $existingRoom = HotelRoom::where('hotel_id', $request->hotelRoom['hotel'])
                ->where('room_type_id', $request->hotelRoom['room_type'])
                ->where('accommodation_id', $request->hotelRoom['accommodation'])
                ->first();

            if ($existingRoom) {
                return response()->json(['error' => "Ya existe ese tipo de habitación con esa acomodación"]);
            }
            $hotel = Hotel::find($request->hotelRoom['hotel']);
            if (!$hotel) {
                return response()->json(['error' => 'Hotel no encontrado'], 404);
            }

            // Calcular la suma actual de las habitaciones existentes en el hotel
            $currentTotalRooms = $hotel->hotelRooms()->sum('quantity');
            // Calcular las habitaciones disponibles
            $availableRooms = $hotel->total_rooms - $currentTotalRooms;
            // Verificar si la suma supera el total permitido
            if ($currentTotalRooms + $request->hotelRoom['quantity'] > $hotel->total_rooms) {
                return response()->json([
                    'error' => 'La cantidad total de habitaciones excede el límite permitido para este hotel. Total habitaciones: ' . $hotel->total_rooms . ' habitaciones disponibles: ' . $availableRooms
                ]);
            }
            HotelRoom::Create([
                'hotel_id' => $request->hotelRoom['hotel'],
                'room_type_id' => $request->hotelRoom['room_type'],
                'accommodation_id' => $request->hotelRoom['accommodation'],
                'quantity' => $request->hotelRoom['quantity']
            ]);
            DB::commit();

            return response()->json(['message' => 'Hotel creado exitosamente.'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::info("error", ["Error: " => $e]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $hotelRoom = HotelRoom::find($id);
        if (!$hotelRoom) {
            return response()->json(['error' => 'Habitación no encontrada.'], 404);
        }
        return response()->json($hotelRoom);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            // Buscar la habitación del hotel por ID
            $hotelRoom = HotelRoom::find( $request->hotelRoom['id']);
            if (!$hotelRoom) {
                return response()->json(['error' => 'Habitación no encontrada'], 404);
            }

            // Verificar si ya existe otra habitación con los mismos atributos
            $existingRoom = HotelRoom::where('hotel_id', $request->hotelRoom['hotel'])
                ->where('room_type_id', $request->hotelRoom['room_type'])
                ->where('accommodation_id', $request->hotelRoom['accommodation'])
                ->where('id', '!=', $request->hotelRoom['id']) // Excluir el registro actual
                ->first();

            if ($existingRoom) {
                return response()->json(['error' => "Ya existe ese tipo de habitación con esa acomodación"]);
            }

            $hotel = Hotel::find($request->hotelRoom['hotel']);
            if (!$hotel) {
                return response()->json(['error' => 'Hotel no encontrado'], 404);
            }

            // Calcular la suma actual de habitaciones, excluyendo la cantidad actual de esta habitación
            $currentTotalRooms = $hotel->hotelRooms()
                ->where('id', '!=', $request->hotelRoom['id']) // Excluir el registro actual
                ->sum('quantity');

            // Calcular las habitaciones disponibles
            $availableRooms = $hotel->total_rooms - $currentTotalRooms;

            // Verificar si la nueva cantidad supera el límite permitido
            if ($currentTotalRooms + $request->hotelRoom['quantity'] > $hotel->total_rooms) {
                return response()->json([
                    'error' => 'La cantidad total de habitaciones excede el límite permitido para este hotel. Total habitaciones: '
                        . $hotel->total_rooms . ', habitaciones disponibles: ' . $availableRooms
                ]);
            }

            // Actualizar los datos
            $hotelRoom->update([
                'hotel_id' => $request->hotelRoom['hotel'],
                'room_type_id' => $request->hotelRoom['room_type'],
                'accommodation_id' => $request->hotelRoom['accommodation'],
                'quantity' => $request->hotelRoom['quantity']
            ]);

            DB::commit();
            return response()->json(['message' => 'Habitación actualizada exitosamente.'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::info("error", ["Error: " => $e]);
            return response()->json(['error' => 'Ocurrió un error al actualizar la habitación.'], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        HotelRoom::where('id', $id)->delete();
    }

    public function getHotelRoomData($id)
    {
        $sql = HotelRoom::with(['roomType', 'accommodation'])
            ->where('hotel_id', $id)
            ->get();
        return response()->json($sql);
    }

    public function getRoomType()
    {
        $sql = RoomType::get();
        return response()->json($sql);
    }

    public function getAccommodation($id)
    {
        $sql = Accomodation::where('room_type_id', $id)->get();
        return response()->json($sql);
    }
}
