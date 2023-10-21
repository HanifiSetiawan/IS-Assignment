<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index() {
        $user = Auth::user()->name;
        return view('home', ['user' => $user]);
    }
}
