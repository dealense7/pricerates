<?php

declare(strict_types=1);

namespace App\Http\Middleware\Auth;

use App\Support\Helpers\Helper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OtpVerifiedMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Helper::user();
        if ($user && $user->getUsesOtpCheck()) {
            if (! $user->token()->otp_verified) {
                return response()->json(['message' => 'OTP verification required'], Response::HTTP_UNAUTHORIZED);
            }
        }

        return $next($request);
    }
}
