<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Event;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/event', function () {
    return view('events', ['events' => \App\Event::all()] );
});

Route::post('/subscribe/{TOPIC_STR}', 'SubscriptionController');

Route::post('/publish/{TOPIC_STR}', 'PublishController');
