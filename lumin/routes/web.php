<?php

use Illuminate\Support\Facades\Route;

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
    #return csrf_token();
    return view('welcome');
});

Route::get('/event', function () {
    return view('welcome');
});

Route::post('/subscribe/{TOPIC_STR}', function ($topic_str) {
    $topic = new \App\Topic();
    $topic->name = $topic_str;
    $topic->save();

    $subscription = new \App\Subscription();
    $subscription->url = $_REQUEST['url'];
    $subscription->topic_id = $topic->getAttributeValue('id');
    $subscription->save();
});


Route::post('/publish/{TOPIC_STR}', function ($topic_str) {
    // Get URLs to push data to
    $topic = \App\Topic::where('name', '=', $topic_str)->first();
    if( is_numeric($topic->id) ){
        $topic_id = $topic->id;
    } else {
        throw new Exception("Unable to find a topic with that name.");
    }

    # save event
    $event = new \App\Event();
    $event->topic_id = $topic_id;
    $event->message = $_REQUEST['message'];
    $event->save();

    # publish event to all subscription urls
    $subscriptions = \App\Subscription::where('topic_id', '=', $topic_id)->get();

    foreach($subscriptions as $subscription){
        $url = $subscription->url;

        $response = Http::post($url, [
            'topic' => $topic_str,
            'message' => $_REQUEST['message']
        ]);
    }
});
