<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
#use Exception;

class SubscriptionController extends Controller
{
    /**
     * Handle the incoming request. Create new topic and subscribe to it.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $topic_str)
    {
        # decode passed arguments
        $bodyContent = $request->json()->all();
        if( !isset($bodyContent['url']) || empty($bodyContent['url']) ){
            exit('Error: Unable to subscribe. Parameter "url" is required.' . PHP_EOL);
            #throw new Exception('Error: Unable to subscribe. Parameter "url" is required.');
        }
        # does this topic already exist?
        if( \App\Topic::where('name', '=', $topic_str)->count() > 0 ){
            exit('Warning: A topic with this name already exists.' . PHP_EOL);
            #throw new Exception('Warning: A topic with this name already exists.');
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
    }
}
