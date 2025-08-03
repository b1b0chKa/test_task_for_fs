<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

Route::middleware('auth.token')->group(function ()
	{
		Route::get('/bookings', [BookingController::class, 'index']);
		Route::post('/bookings', [BookingController::class, 'store']);
		Route::post('/bookings/{bookingId}/slots', [BookingController::class, 'addSlot']);
		Route::patch('/bookings/{bookingId}/slots/{slotId}', [BookingController::class, 'updateSlot']);
		Route::delete('/bookings/{bookingId}', [BookingController::class, 'destroy']);
	}
);
