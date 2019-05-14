<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\Product;

class HomeController extends Controller {
	public function home() {
		$products = Product::orderBy('title', 'asc')->get();

		return view('home', array('products' => $products));
	}
}
