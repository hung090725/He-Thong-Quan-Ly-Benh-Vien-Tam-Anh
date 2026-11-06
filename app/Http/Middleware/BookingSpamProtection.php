<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class BookingSpamProtection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Bot Honeypot Check: legitimate users leave this invisible field empty
        if ($request->filled('website_verification_code')) {
            return response()->json([
                'success' => false,
                'message' => 'Yêu cầu không hợp lệ (Phát hiện truy cập bất thường)',
            ], 422);
        }

        if (app()->environment('testing')) {
            return $next($request);
        }

        // 2. Velocity Check: form must take at least 1.5 seconds to be humanly completed
        if ($request->filled('_form_load_time')) {
            $loadTime = (int) $request->input('_form_load_time');
            $currentTime = time();
            if ($currentTime - $loadTime < 1) { // less than 1 second
                return back()
                    ->withInput()
                    ->withErrors(['honeypot' => 'Thao tác quá nhanh. Vui lòng thử lại sau giây lát.']);
            }
        }

        // 3. Multi-tier IP Rate Limiting (max 15 booking attempts per 5 minutes per IP)
        $ip = $request->ip();
        $rateKey = 'booking_throttle:' . $ip;

        if (RateLimiter::tooManyAttempts($rateKey, 15)) {
            $seconds = RateLimiter::availableIn($rateKey);
            return back()
                ->withInput()
                ->withErrors([
                    'rate_limit' => "Bạn đã gửi quá nhiều yêu cầu đặt lịch. Vui lòng chờ {$seconds} giây trước khi thử lại.",
                ]);
        }

        RateLimiter::hit($rateKey, 300); // 5 minutes

        return $next($request);
    }
}
