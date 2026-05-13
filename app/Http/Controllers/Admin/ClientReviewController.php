<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;

class ClientReviewController extends Controller
{
    public function show(Client $client)
    {
        return view('admin.clients.review', [
            'client' => $client,
        ]);
    }
}
