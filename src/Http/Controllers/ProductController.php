<?php

namespace Haxibiao\Store\Http\Controllers;

use Haxibiao\Breeze\Http\Controllers\Controller;

class ProductController extends Controller
{

    public function index()
    {
        return view('product.index');
    }

    public function show($id)
    {
        return view('product.show');
    }
}
