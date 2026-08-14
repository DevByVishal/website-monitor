<?php

namespace App\Http\Controllers;

use App\Models\Client;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::with('websites')
            ->select('id', 'email')
            ->orderBy('email')
            ->get();

        return response()->json($clients);
    }
}