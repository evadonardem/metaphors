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
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
		public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
	    $code = 'MTPHRS'.rand(100000,999999);
		$product = array(
			'code' => $code,
			'title' => Input::get('title'),
			'description' => Input::get('description'),
			'price' => Input::get('price')
		);
		Product::create($product);

        $product = Product::find($code);

        $image = Input::file('image')[0];


        $bannerImage = Image::make($image);
        $bannerImage->fit(800, 450);
        $bannerImage->save('images/products/'.$product->code.'.jpg');

        $thumbnailImage = $bannerImage;
        $thumbnailImage->fit(100);
 	    $thumbnailImage->save('images/products/'.$product->code.'_thumb.jpg');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
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
