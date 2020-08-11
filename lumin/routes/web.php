<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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

Route::post('/subscribe/{TOPIC_STR}', function (Request $request, $topic_str) {
    # decode passed arguments
    $bodyContent = $request->json()->all();
    if( !isset($bodyContent['url']) || empty($bodyContent['url']) ){
        throw new Exception('Unable to subscribe. Parameter "url" is required.');
    }

    # save new topic
    $topic = new \App\Topic();
    $topic->name = $topic_str;
    $topic->save();

    # save subscription
    $subscription = new \App\Subscription();
    $subscription->url = $bodyContent['url'];
    $subscription->topic_id = $topic->getAttributeValue('id');
    $subscription->save();
});

Route::post('/publish/{TOPIC_STR}', function (Request $request, $topic_str) {
    # decode passed arguments
    $bodyContent = $request->json()->all();
    if( !isset($bodyContent['message']) || empty($bodyContent['message']) ){
        throw new Exception('Unable to publish. Parameter "message" is required.');
    }

    # get URLs to push data to
    $topic = \App\Topic::where('name', '=', $topic_str)->first();
    if( is_numeric($topic->id) ){
        $topic_id = $topic->id;
    } else {
        throw new Exception("Unable to find a topic with that name.");
    }

    # save event
    $event = new \App\Event();
    $event->topic_id = $topic_id;
    $event->message = $bodyContent['message'];
    $event->save();

    # publish event to all subscription urls
    $subscriptions = \App\Subscription::where('topic_id', '=', $topic_id)->get();

    foreach($subscriptions as $subscription){
        $url = $subscription->url;

        $response = Http::post($url, [
            'topic' => $topic_str,
            'message' => $bodyContent['message']
        ]);
    }
});
