<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CallMeBotService
{
    protected $phone;
    protected $apikey;

    public function __construct()
    {
        $this->phone = env('CALLMEBOT_PHONE');
        $this->apikey = env('CALLMEBOT_APIKEY');        
    }

    public function sendMessage($message)
    {
        try {
            $response = Http::get('https://api.callmebot.com/whatsapp.php', [
                'phone' => $this->phone,
                'text' => $message,
                'apikey' => $this->apikey,
            ]);

            Log::info("📤 WhatsApp sent. Response: " . $response->body());

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("❌ Failed to send WhatsApp message: " . $e->getMessage());
            return false;
        }
    }
}