<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email address as verified.
     * Works even if user is not logged in (verification via signed URL).
     */
    public function __invoke(Request $request, $id, $hash): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Verify the hash matches
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Invalid verification link. Please try registering again.']);
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')
                ->with('status', 'Email already verified. You can now log in.');
        }

        // Mark as verified
        $user->markEmailAsVerified();
        event(new Verified($user));

        return redirect()->route('login')
            ->with('status', 'Email verified successfully! You can now log in.');
    }
}
