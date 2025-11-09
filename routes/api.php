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
use App\Http\Controllers\SubscribeController;
use App\Http\Controllers\ContactController;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Checkout\Session;


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


// match controller 

// public matche  route 

Route::get('/match/search',[MatcheController::class,'search']);
Route::get('/match-details/{id}',[MatcheController::class,'show']);
Route::get('/show-matches',[MatcheController::class,'index']);


Route::middleware('auth:sanctum','role:admin')->group(function(){
    Route::post('/add-match',[MatcheController::class,'store']);
    Route::put('/match/{id}',[MatcheController::class,'update']);
    Route::delete('/match/{id}',[MatcheController::class,'destroy']);

   // sunscribe controller 

    Route::get('/show-emails', [SubscribeController::class,'getEmails']);
});




// sunscribe controller 

 Route::post('/add-email', [SubscribeController::class,'store']);




Route::middleware('auth:sanctum','role:admin')->group(function(){
     Route::post('/add-zone',[ZoneController::class,'store']);
    Route::put('/zone/{id}',[ZoneController::class,'update']);
    Route::delete('/zone/{id}',[ZoneController::class,'destroy']);
});

// public endpoint 
    Route::get('/show-zones',[ZoneController::class,'index']);
    Route::get('/zone-details/{id}',[ZoneController::class,'show']);
   Route::get('/zone/search',[ZoneController::class,'search']);




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


// contact controller

Route::get('/contacts', [ContactController::class, 'index']); 
Route::post('/contact', [ContactController::class, 'store']); 


// stripe 

Route::post('/create-checkout-session', function (Request $request) {
    Stripe::setApiKey(env('STRIPE_SECRET'));

    $session = Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'mad',
                'unit_amount' => $request->amount * 100,
                'product_data' => [
                    'name' => $request->title,
                ],
            ],
            'quantity' => $request->count,
        ]],
        'mode' => 'payment',
        'success_url' => 'http://localhost:5173/success?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => 'http://localhost:5173/cancel',
         'metadata' => [
            'user_id' => $request->user_id,
            'zone_id' => $request->zone_id,
            'number_of_tickets' => $request->number_of_tickets,
            'total_price' => $request->total_price,
        ],
    ]);

    return response()->json(['url' => $session->url]);
});


// WEBHOOKS

Route::post('/stripe/webhook', [ReservationController::class, 'handleWebhook']);