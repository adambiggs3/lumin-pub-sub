<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PublishController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $topic_str)
    {
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
    }
}
