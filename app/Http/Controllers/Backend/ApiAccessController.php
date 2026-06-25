<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiAccessController extends Controller
{
    public function index()
    {
        return view('pages.backend.api_access.index');
    }
}
