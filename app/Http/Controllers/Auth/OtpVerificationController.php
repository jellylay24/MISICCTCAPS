<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpVerificationController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        if (!$user || $user->email_verified_at) {
            return redirect('/');
        }
        return view('auth.verify-otp', ['user' => $user]);
    }

    public function send(Request $request, SmsService $sms)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Delete old OTPs
        Otp::where('user_id', $user->id)->delete();

        // Generate new OTP
        $otp = SmsService::generateOtp();
        $expiresAt = now()->addMinutes(5);

        Otp::create([
            'user_id' => $user->id,
            'phone' => $user->phone,
            'otp' => $otp,
            'expires_at' => $expiresAt,
        ]);

        // Only attempt SMS if API key is configured
        if (config('services.semaphore.api_key')) {
            $sent = $sms->sendOtp($user->phone, $otp);
            if (!$sent) {
                return back()->with('error', 'Failed to send OTP. Please try again.');
            }
        } else {
            // Fallback: log OTP for testing
            \Illuminate\Support\Facades\Log::info("OTP for {$user->phone}: {$otp}");
        }

        return back()->with('status', 'OTP sent to ' . $user->phone);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $otpRecord = Otp::where('user_id', $user->id)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.'])->withInput();
        }

        // Verify user
        $user->email_verified_at = now();
        $user->save();

        // Clean up OTPs
        Otp::where('user_id', $user->id)->delete();

        // Redirect based on role
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('faculty.dashboard');
    }

    public function resend(Request $request, SmsService $sms)
    {
        return $this->send($request, $sms);
    }
}
