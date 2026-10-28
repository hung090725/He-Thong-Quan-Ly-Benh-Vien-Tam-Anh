<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle authentication attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            /** @var User $user */
            $user = Auth::user();

            // Account suspension check
            if (! $user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()
                    ->withInput($request->only('email'))
                    ->withErrors([
                        'email' => 'Tài khoản của bạn đã bị tạm khóa bởi ban quản lý. Vui lòng liên hệ quản trị viên bệnh viện để được hỗ trợ.',
                    ]);
            }

            $request->session()->regenerate();

            return $this->redirectBasedOnRole($user)
                ->with('success', "Xin chào {$user->name}! Bạn đã đăng nhập thành công với vai trò {$user->role_name}.");
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'Thông tin đăng nhập (email hoặc mật khẩu) không chính xác.',
            ]);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Bạn đã đăng xuất khỏi hệ thống thành công.');
    }

    /**
     * Redirect authenticated user to appropriate dashboard based on role.
     */
    protected function redirectBasedOnRole(User $user): RedirectResponse
    {
        return match ($user->role) {
            User::ROLE_ADMIN => redirect()->intended(route('admin.dashboard')),
            User::ROLE_RECEPTIONIST => redirect()->intended(route('reception.appointments.index')),
            User::ROLE_DOCTOR => redirect()->intended(route('doctor.queue')),
            User::ROLE_PATIENT => redirect()->intended(route('patient.history')),
            default => redirect()->intended('/'),
        };
    }
}
