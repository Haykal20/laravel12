<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/mahasiswa';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'login';
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $login = $request->login;
        $field = is_numeric($login) ? 'nim' : 'name';
        
        return [
            $field => $login,
            'password' => $request->password
        ];
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $login = $request->login;
        $field = is_numeric($login) ? 'nim' : 'name';
        
        // Cek apakah user exists
        $user = \App\Models\User::where($field, $login)->first();
        
        if (!$user) {
            $message = is_numeric($login) ? 
                'NIM tidak ditemukan' : 
                'Username tidak ditemukan';
        } else {
            $message = 'Password yang Anda masukkan salah';
        }

        throw ValidationException::withMessages([
            'login' => [$message]
        ]);
    }

    protected function authenticated(Request $request, $user)
    {
        return redirect()->intended($this->redirectTo)
            ->with('success', "Selamat datang, {$user->name}!");
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout');
    }
}
