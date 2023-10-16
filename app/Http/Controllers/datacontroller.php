<?php

namespace App\Http\Controllers;
use App\Models\Orang;
use Illuminate\Http\Request;

class datacontroller extends Controller
{
    public function index(){
        $orangs = Orang::take(5)->get(); // Mengambil 5 data pertama dari tabel Orang
        return view('show', ['orangs' => $orangs]);
    }
}
