<?php

namespace App\Http\Controllers;

use App\Models\DataRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccessController extends Controller
{
    public function index() {
        return view('access');
    }

    public function submit(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:App\Models\User,email']
        ]);

        $user = auth()->user();

        if ($user->email == $request->email) {
            return redirect()->back()->withErrors(['yourself' => "You can't send requests to yourself"]);
        }
        
        if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        $validated = $validator->validated();

        $exists = DataRequest::where('from', '=', $user->email)
                            ->where('to','=', $validated['email'])
                            ->exists();
        
        if($exists) {
            $data = DataRequest::where('from', '=', $user->email)
                                ->where('to','=', $validated['email'])->first();
            switch ($data->state) {
                case 'accepted':
                    return back()->withErrors(['alr_exist' => 'You have already requested data from this user']);
                case 'pending':
                    return back()->withErrors(['alr_exist' => 'Your request is waiting to be accepted']);
                case 'rejected':
                    return back()->withErrors(['alr_exist' => 'Your request has already been rejected by this user']);
                default:
                    return back()->withErrors(['alr_exist' => 'State of the request is invalid ' . '{' . $data->state . '}']);
            }
        }

        $dataRequest = new DataRequest;
        $dataRequest->from = $user->email;
        $dataRequest->to = $validated['email'];
        $dataRequest->state = 'pending';

        $dataRequest->save();

        return redirect()->back()->with('success','Request sent successfully');
    }

    
}
