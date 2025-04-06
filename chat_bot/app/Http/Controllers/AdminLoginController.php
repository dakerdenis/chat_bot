<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Admin;
use Illuminate\Support\Facades\RateLimiter;
class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
    
        // 🔐 Rate limiter на логин
        $ip = $request->ip();
        $key = 'admin-login:' . $ip;
    
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors([
                'email' => 'Слишком много попыток входа. Попробуйте позже.'
            ]);
        }
    
        RateLimiter::hit($key, 60); // Блокируем на 60 сек
    
        $admin = Admin::where('email', $request->email)->first();
    
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return back()->withErrors(['email' => 'Неверный email или пароль.']);
        }
    
        Session::put('admin_id', $admin->id);
        return redirect('/admin/dashboard');
    }
    
    public function logout()
    {
        Session::forget('admin_id');
        return redirect()->route('admin.login');
    }
}
