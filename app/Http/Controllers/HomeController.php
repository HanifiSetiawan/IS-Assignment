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
        $incomingDataRequests = DataRequest::where('to','=', $user->email)->get();

        return view('home', ['user' => $user->name, 'sent' => $sentDataRequests, 'incoming' => $incomingDataRequests]);
    }

    public function incoming(Request $request) {

    }
}
