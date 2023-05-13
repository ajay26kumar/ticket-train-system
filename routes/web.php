<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketReservationController;


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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/view-train', [TicketReservationController::class, 'viewTrainSearch']);
Route::post('/search-train', [TicketReservationController::class, 'SearchTrainRoute']);
Route::post('/view-reservation-seat',[TicketReservationController::class, 'viewReservationSeats']);