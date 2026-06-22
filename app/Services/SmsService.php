<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected string $apiKey;
    protected string $senderName;

    public function __construct()
    {
        $this->apiKey = config('services.semaphore.api_key');
        $this->senderName = config('services.semaphore.sender_name', 'ICCTMIS');
    }

    public function sendOtp(string $phone, string $otp): bool
    {
        $message = "ICCT MIS: Your OTP code is $otp. Valid for 5 minutes.";

        try {
            $response = Http::asForm()->post('https://api.semaphore.co/api/v4/messages', [
                'apikey' => $this->apiKey,
                'number' => $phone,
                'message' => $message,
                'sendername' => $this->senderName,
            ]);

            $body = $response->json();

            if ($response->successful()) {
                Log::info("SMS sent to {$phone}: OTP {$otp}", ['response' => $body]);
                return true;
            }

            Log::warning("SMS failed for {$phone}", ['response' => $body]);
            return false;
        } catch (\Exception $e) {
            Log::error("SMS exception for {$phone}: " . $e->getMessage());
            return false;
        }
    }

    public static function generateOtp(int $length = 6): string
    {
        $digits = '';
        for ($i = 0; $i < $length; $i++) {
            $digits .= random_int(0, 9);
        }
        return $digits;
    }
}
