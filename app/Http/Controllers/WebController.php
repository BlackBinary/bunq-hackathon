<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function createReceipt()
    {
        return view('createReceipt');
    }

    public function getReceipt($hash)
    {
        return view('getReceipt', ['hash' => $hash]);
    }
}
