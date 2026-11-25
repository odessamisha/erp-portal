<?php

namespace App\Observers;

use App\Models\ClientMessage;

class ClientMessageObserver
{
    /**
     * Handle the ClientMessage "created" event.
     */
    public function created(ClientMessage $clientMessage): void
    {
        //убрать для отправки через вайбер!!!!
        return;
        $client = $clientMessage->client;
        $start_text ="
=============
-Armatel GPS-  
=============\n\n";
        //отправка сообщения только для клиентов с exchange_id = 1 (Viber)
        if ($client->exchange_id == 1) {
            $messenger = new \App\Services\EchatMessanger();
            $message_id = $clientMessage->message_id ?? str()->random();
            $message_text = $start_text.$clientMessage->message ?? 'No message';
            $message_number = $client->contact_phone ?? '+380674730054'; // Замените на реальный номер клиента
            \Log::info('Sending message to client: ' . $client->name . ' (' . $message_number . ')');

            
            $result = $messenger->messages_send([
                "message" => [
                    "id" => $message_id,
                    "text" => $message_text,
                ],
                "contact" => [
                    "number" => $message_number
                ]  
            ]);
            
            $response = json_decode($result, true);
            \Log::info('Message response: ' . json_encode($response));
            if (isset($response['status']) && $response['status'] === 'Success') {
                $clientMessage->status = 'sending';
                $clientMessage->save();
            }
            else {
                $clientMessage->status = 'error';
                $clientMessage->error_message = $response['message'] ?? 'Unknown error';
                $clientMessage->save();
            }
            \Log::info($result);
            
        }
        
    }

    /**
     * Handle the ClientMessage "updated" event.
     */
    public function updated(ClientMessage $clientMessage): void
    {
        //
    }

    /**
     * Handle the ClientMessage "deleted" event.
     */
    public function deleted(ClientMessage $clientMessage): void
    {
        //
    }

    /**
     * Handle the ClientMessage "restored" event.
     */
    public function restored(ClientMessage $clientMessage): void
    {
        //
    }

    /**
     * Handle the ClientMessage "force deleted" event.
     */
    public function forceDeleted(ClientMessage $clientMessage): void
    {
        //
    }
}
