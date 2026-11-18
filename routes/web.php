<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

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
Route::middleware('guest')->group(function() {
    Route::get('/', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'try_login']);
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'try_register']);
});

Route::middleware('auth')->group(function() {
    Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth');
    Route::get('/home', [TicketController::class, 'index']);
    Route::get('/paid-tickets', [TicketController::class, 'show_tickets']);
    Route::get('/saved', [TicketController::class, 'show_saved_schedules']);
    Route::get('/schedules', [TicketController::class, 'get_schedules']);
    Route::get('/tickets', [TicketController::class, 'get_tickets']);
    Route::post('/ticket/{schedule}/buy', [TicketController::class, 'buy_ticket']);
    Route::get('/saved-schedules', [TicketController::class, 'get_saved_schedules']);
    Route::post('/toggle-save-schedule/{schedule}', [TicketController::class, 'toggle_save_schedule']);
});
