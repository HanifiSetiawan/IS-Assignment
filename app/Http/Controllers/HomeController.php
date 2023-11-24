<?php

namespace App\Http\Controllers;

use App\Models\DataRequest;
use Auth;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index() {
        $user = Auth::user();

        $sentDataRequests = DataRequest::where('from', '=', $user->email)->get();

        return view('home', ['user' => $user->name, 'sent' => $sentDataRequests]);
    }

    public function incoming() {

    }
}
