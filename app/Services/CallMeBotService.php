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
        $this->phone = 6281586715179;
        $this->apikey = 9479963;        
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