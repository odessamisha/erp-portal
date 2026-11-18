<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientMessage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;


class WebhookController extends Controller
{
    //
    public function incomingMessages(Request $request)
    {

    }
    public function statusMessage(Request $request)
    {
        // Handle the incoming messages from the webhook
        $data = $request->all();
        // For debugging purposes, you can log or inspect the incoming data
        // You can process the data as needed, for example, save it to the database
        // For demonstration, let's just log the data
        \Log::info('Incoming message received:', $data);

        
        // Process the webhook (e.g., update transaction status in your database)
        $message_id = $request->input('message_id');
        $status_id = $request->input('status');
        $description = $request->input('description');


        $status = 'sending';

        if ($status_id === '1') {
            $status = 'delivered';
        }
        else {
            $status = 'error';
        }

        $message = ClientMessage::query()->where('message_id', $message_id)->first();
        $message->status = $status;
        $message->error_message = $description;
        $message->delivered_at = now();
        $message->save();        

        return response()->json(['status' => 'success']);
    }
}
