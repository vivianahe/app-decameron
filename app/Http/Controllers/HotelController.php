<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\HotelRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HotelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sql = Hotel::get();
        return response()->json($sql);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Verificar si ya existe un hotel con el mismo nombre o NIT
            $existingHotel = Hotel::where('name', $request->name)
                ->orWhere('nit', $request->nit)
                ->first();

            if ($existingHotel) {
                // Verificar qué campo causó el conflicto
                $errorField = $existingHotel->name === $request->name ? 'nombre' : 'nit';
                $errorValue = $errorField === 'nombre' ? $request->name : $request->nit;
                return response()->json(['error' => "Ya existe un hotel con $errorField: $errorValue"]);
            } else {
                // Insertar datos
                Hotel::Create([
                    'name' => $request->name,
                    'address' => $request->address,
                    'city' => $request->city,
                    'nit' => $request->nit,
                    'total_rooms' => $request->total_rooms,
                    'user_id' => Auth::user()->id,
                ]);
                DB::commit();

                return response()->json(['message' => 'Hotel creado exitosamente.'], 200);
            }
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
        $sql = Hotel::where('id', $id)->first();
        return response()->json($sql);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $existingHotel = Hotel::where('name', $request->name)
                ->orWhere('nit', $request->nit)
                ->where('id', '!=', $request->id) // Excluye el ID que estás actualizando
                ->first();

            if ($existingHotel) {
                // Verificar qué campo causó el conflicto
                $errorField = $existingHotel->name === $request->name ? 'nombre' : 'nit';
                $errorValue = $errorField === 'nombre' ? $request->name : $request->nit;

                return response()->json(['error' => "Ya existe un hotel con $errorField: $errorValue"]);
            }

            // Verificar si existe id
            $hotel = Hotel::find($request->id);
            if (!$hotel) {
                return response()->json(['error' => 'Hotel no encontrado.'], 404);
            }
            
            // Actualización de los datos
            $hotel->update([
                'name' => $request->name,
                'address' => $request->address,
                'city' => $request->city,
                'nit' => $request->nit,
                'total_rooms' => $request->total_rooms,
                'user_id' => Auth::user()->id,
            ]);

            DB::commit();

            return response()->json(['message' => 'Hotel actualizado exitosamente.'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error: ", ['exception' => $e->getMessage()]);
            return response()->json(['error' => 'Ocurrió un error al actualizar el hotel.'], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $hotel = Hotel::find($id);
        $hotel_room = HotelRoom::where('hotel_id', $id)->get();

        //Verificar si el hotel tiene habitaciones asociadas
        if (count($hotel_room) > 0) {
            return response()->json([
                'data' => null,
                'message' => "Exists"
            ]);
        } else {
            if ($hotel) {
                $hotel->delete();
                return response()->json([
                    'data' => "ok",
                    'message' => "Hotel eliminado exitosamente!"
                ]);
            }
        }
    }
}
