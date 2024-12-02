<?php

namespace App\Http\Controllers\Auth;

// use Session;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Akseslevel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;

class LoginController extends Controller
{

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->akses = new Akseslevel();
    }

    public function index()
    {
        return view('auth.login');
    }

    public function proses_login(Request $request)
    {
        $request->validate([
            'UserName' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('UserName', 'password');

        $user = User::where('UserName', $credentials['UserName'])->first();
        if (!$user) {
            return redirect("login")->withErrors('User tidak ditemukan.');
        }
        if (!Hash::check($credentials['password'], $user->Password, [])) {
            return redirect("login")->withErrors('Password tidak sesuai.');
        }

        $akseslevel = $this->akses->get_fitur($user->KodeLevel);
        $request->session()->put('akses', $akseslevel);

        if (Auth::attempt($credentials)) {
            return redirect('home')
                ->withSuccess('Signed in');
        }
        return redirect("login")->withErrors('Login details are not valid');
    }

    protected function authenticated()
    {
        if (Auth::User()->IsAktif == 0) {
            Auth::logout();
            Session::flash('error', "Akun yang kamu gunakan sudah Tidak Aktif !");
            return redirect('login');
        }
    }

    public function username()
    {
        return 'Username';
    }

    public function logout(Request $request)
    {
        if (Auth::guard()->check()) {
            Auth::guard()->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
