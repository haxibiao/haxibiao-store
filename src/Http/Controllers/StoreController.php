<?php

namespace Haxibiao\Store\Http\Controllers;

use Haxibiao\Breeze\Http\Controllers\Controller;

class StoreController extends Controller
{

    public function index()
    {
        return view('store.index');
    }

    public function show($id)
    {
        return view('store.show');
    }
}
