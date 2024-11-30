<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\HotelRoomController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes(['register' => false]);
Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

Route::get('getInitialRedirectPath', [UserController::class, 'getInitialRedirectPath'])->name('getInitialRedirectPath');

//VIEW ROUTES
Route::group(['middleware' => ['auth']], function () {
    Route::view('/hotel', 'home')->name('hotel');
    Route::view('/hotel_rooms', 'home')->name('hotel_rooms');
    Route::view('/users', 'home')->name('users');
});

//HOTELS
Route::group(['middleware' => ['web', 'permission:hotel_management', 'auth']], function () {
    Route::get('getHotel', [HotelController::class, 'index'])->name('getHotel');
    Route::post('setHotel', [HotelController::class, 'store'])->name('setHotel');
    Route::get('deleteHotel/{roleId}', [HotelController::class, 'destroy'])->name('deleteHotel');
    Route::get('getHotelId/{id}', [HotelController::class, 'edit'])->name('getHotelId');
    Route::post('updateHotel', [HotelController::class, 'update'])->name('updateHotel');
});
//HOTEL ROOMS
Route::group(['middleware' => ['web', 'permission:manage_hotel_room', 'auth']], function () {
    Route::get('getHotelRoomData/{id}', [HotelRoomController::class, 'getHotelRoomData'])->name('getHotelRoomData');
    Route::get('getRoomType', [HotelRoomController::class, 'getRoomType'])->name('getRoomType');
    Route::get('getAccommodation/{id}', [HotelRoomController::class, 'getAccommodation'])->name('getAccommodation');
    Route::post('setHotelRoom', [HotelRoomController::class, 'store'])->name('setHotelRoom');
    Route::get('getHotelRoomId/{id}', [HotelRoomController::class, 'edit'])->name('getHotelRoomId');
    Route::post('updateHotelRoom', [HotelRoomController::class, 'update'])->name('updateHotelRoom');
    Route::get('deleteHotelRoom/{id}', [HotelRoomController::class, 'destroy'])->name('deleteHotelRoom');
});

//USER
Route::group(['middleware' => ['web', 'permission:user_management', 'auth']], function () {
    Route::get('getUserData', [UserController::class, 'index'])->name('getUserData');
    Route::post('addUser', [UserController::class, 'store'])->name('addUser');
    Route::delete('deleteUser/{id}', [UserController::class, 'destroy'])->name('deleteUser');
    Route::get('getUserId/{id}', [UserController::class, 'edit'])->name('getUserId');
    Route::post('updateUser', [UserController::class, 'update'])->name('updateUser');
    Route::get('getAccessHistory/{id}', [UserController::class, 'getAccessHistory'])->name('getAccessHistory');
    Route::post('updateState', [UserController::class, 'updateState'])->name('updateState');
    Route::get('getRol', [UserController::class, 'getRol'])->name('getRol');
});

