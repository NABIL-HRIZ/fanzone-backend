<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MatcheController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ScanController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register',[RegisteredUserController::class,'store']);
Route::post('/login',[AuthenticatedSessionController::class,'login']);
Route::post('/logout',[AuthenticatedSessionController::class,'destroy'])->middleware('auth:sanctum');



// ADMIN PERMISSIONS 

// CRUD USERS 

Route::middleware(['auth:sanctum','role:admin'])->group(function() {
    Route::get('/show-fans', [AdminController::class,'getAllUsers']);
    Route::post('/add-fan', [AdminController::class,'storeUser']);
    Route::put('/fan/{id}', [AdminController::class,'updateUser']);
    Route::delete('/fan/{id}', [AdminController::class,'deleteUser']);
    Route::get('/fan-details/{id}', [AdminController::class,'getUserDetail']);

});

Route::middleware('auth:sanctum','role:admin')->group(function(){
     Route::post('/add-zone',[ZoneController::class,'store']);
    Route::get('/zone-details/{id}',[ZoneController::class,'show']);
    Route::put('/zone/{id}',[ZoneController::class,'update']);
    Route::delete('/zone/{id}',[ZoneController::class,'destroy']);
   Route::get('/zone/search',[ZoneController::class,'search']);
});

// public endpoint 
    Route::get('/show-zones',[ZoneController::class,'index']);


Route::middleware('auth:sanctum','role:admin')->group(function(){
    Route::post('/add-match',[MatcheController::class,'store']);
    Route::get('/match-details/{id}',[MatcheController::class,'show']);
    Route::put('/match/{id}',[MatcheController::class,'update']);
    Route::delete('/match/{id}',[MatcheController::class,'destroy']);
   Route::get('/match/search',[MatcheController::class,'search']);
});

// public endpoint 
    Route::get('/show-matches',[MatcheController::class,'index']);



    // Reservation Controller

    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/reservations', [ReservationController::class, 'index']);     
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']); 
});


Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::post('/reservations', [ReservationController::class, 'store']);       
    Route::get('/reservations/my', [ReservationController::class, 'myReservations']); 
    Route::put('/reservations/{id}', [ReservationController::class, 'update']);   
});

Route::middleware(['auth:sanctum', 'role:agent'])->group(function () {
    Route::get('/reservations/{id}', [ReservationController::class, 'show']);    
    Route::get('/reservations/search', [ReservationController::class, 'search']); 
});


// Scan controller 

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/scans', [ScanController::class, 'index']);
    Route::get('/scans/{id}', [ScanController::class, 'show']);
    Route::delete('/scans/{id}', [ScanController::class, 'destroy']);
});


Route::middleware(['auth:sanctum', 'role:agent'])->group(function () {
    Route::post('/scans', [ScanController::class, 'store']);
    Route::put('/scans/{id}', [ScanController::class, 'update']);
    Route::get('/scans/search', [ScanController::class, 'search']);
});