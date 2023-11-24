<?php

namespace App\Http\Controllers;

use App\Models\DataRequest;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class HomeController extends Controller
{
    public function index() {
        $user = Auth::user();

        $sentDataRequests = DataRequest::where('from', '=', $user->email)->get();
        $incomingDataRequests = DataRequest::where('to','=', $user->email)->get();

        return view('home', ['user' => $user->name, 'sent' => $sentDataRequests, 'incoming' => $incomingDataRequests]);
    }

    public function incoming(Request $request) {
        
        $validator = Validator::make($request->all(), [
            'state' => ['required', Rule::in(['accepted', 'rejected'])],
            'from' => ['required', 'email'],
            'to' => ['required', 'email']
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator);
        }

        $validated = $validator->validated();

        
        $data = DataRequest::where('from','=', $validated['from'])->where('to','=', $validated['to'])->first();

        $data->state = $validated['state'];
        $data->save();

        return back()->with('success','Request responded successfully');
    }

    public function send(Request $request) {
        $validator = Validator::make($request->all(), [
            'state' => ['required', Rule::in(['email'])],
            'from' => ['required', 'email'],
            'to' => ['required', 'email']
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator);
        }

        $validated = $validator->validated();
    }
}
