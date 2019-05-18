<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view('products.master-list');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
	    $code = 'MTPHRS'.rand(100000,999999);
	    $product = array(
	       'code' => $code,
			     'title' => $request->input('title'),
			     'description' => $request->input('description'),
			     'price' => $request->input('price')
	    	);
		    
		    Product::create($product);
		    $product = Product::find($code);
		    /*$image = Input::file('image')[0];

      $bannerImage = Image::make($image);
      $bannerImage->fit(800, 450);
      $bannerImage->save('images/products/'.$product->code.'.jpg');

      $thumbnailImage = $bannerImage;
      $thumbnailImage->fit(100);
      $thumbnailImage->save('images/products/'.$product->code.'_thumb.jpg');*/
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
	   $product = Product::find($id);
	   $product->delete();
	}

	public function productsJSON() {
		$products = Product::orderBy('title', 'asc')->get();
		return response()->json($products);
	}
}
