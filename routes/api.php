<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/charge', function () {
    // Set your secret key: remember to change this to your live secret key in production
// See your keys here: https://dashboard.stripe.com/account/apikeys
    \Stripe\Stripe::setApiKey("sk_test_RNeCYsJkP5vkYOH0DBjDsVTA");

// Create a Customer:
    $customer = \Stripe\Customer::create([
        'source' => 'tok_mastercard',
        'email' => 'paying.user@example.com',
    ]);

// Charge the Customer instead of the card:
    $charge = \Stripe\Charge::create([
        'amount' => 99999900,
        'currency' => 'usd',
        'customer' => $customer->id,
    ]);

// YOUR CODE: Save the customer ID and other info in a database for later.

// When it's time to charge the customer again, retrieve the customer ID.
//    $charge = \Stripe\Charge::create([
//        'amount' => 1500, // $15.00 this time
//        'currency' => 'usd',
//        'customer' => $customer->id, // Previously stored, then retrieved
//    ]);

    echo "Good";
});