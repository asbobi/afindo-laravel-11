<?php

namespace App\Http\Controllers\Admin;

use DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use App\Models\Mstloket;

class HomeController extends Controller
{
    private $menu = 'Dashboard';
    public function __construct()
    {
        View::share('menu', $this->menu);
        View::share('title', $this->menu);
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return view('admin.home.index');
    }
}
