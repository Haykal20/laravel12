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
        $login = request()->input('login');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 
                (is_numeric($login) ? 'nim' : 'name');
        request()->merge([$field => $login]);
        return $field;
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $login = $request->input('login');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 
                (is_numeric($login) ? 'nim' : 'name');
        return [
            $field => $login,
            'password' => $request->input('password')
        ];
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $login = $request->input('login');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 
                (is_numeric($login) ? 'nim' : 'name');

        $message = 'Maaf, ';
        if (!$this->existsInDatabase($field, $login)) {
            $message .= ($field == 'nim' ? 'NIM' : 'Username') . ' tidak ditemukan';
        } else {
            $message .= 'Password yang Anda masukkan salah';
        }

        throw ValidationException::withMessages([
            'login' => [$message]
        ]);
    }

    protected function existsInDatabase($field, $value)
    {
        return \App\Models\User::where($field, $value)->exists();
    }

    protected function authenticated(Request $request, $user)
    {
        return redirect()->intended($this->redirectTo)
            ->with('success', 'Selamat datang kembali, ' . $user->name);
    }
}
