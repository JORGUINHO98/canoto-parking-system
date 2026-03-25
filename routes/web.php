<?php

use App\Http\Controllers\ParkingController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('parking.ingreso');
});

Route::prefix('parking')->name('parking.')->group(function () {
    Route::get('/ingreso', [ParkingController::class, 'ingreso'])->name('ingreso');
    Route::post('/buscar', [ParkingController::class, 'search'])->name('search');
    Route::post('/ingreso', [ParkingController::class, 'store'])->name('store');

    Route::get('/salida', [TicketController::class, 'salida'])->name('salida');
    Route::post('/salida', [TicketController::class, 'procesarSalida'])->name('salida.process');
});
