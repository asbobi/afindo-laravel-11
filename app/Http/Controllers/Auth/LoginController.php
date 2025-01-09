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
use Illuminate\Support\Facades\Cache;

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
        $x = [];
        return view('auth.login', $x);
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
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.'
            ], 404);
        }

        if (!Hash::check($credentials['password'], $user->Password, [])) {
            return response()->json([
                'success' => false,
                'message' => 'Password tidak sesuai.'
            ], 401);
        }

        $akseslevel = $this->akses->get_fitur($user->KodeLevel);
        session()->put('user', $user);
        Cache::forever('akses_user', $akseslevel);

        if (Auth::attempt($credentials)) {
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Login details are not valid'
        ], 401);
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
        Cache::forget('akses_user');
        return redirect('/');
    }
}
