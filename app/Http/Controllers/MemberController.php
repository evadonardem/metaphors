<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Member;
use Illuminate\Http\Request;
use Redirect;
use Validator;

class MemberController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view('members.master-list');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('members.register');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$person = array(
			'firstName' => $request->input('firstName'),
			'middleName' => $request->input('middleName'),
			'lastName' => $request->input('lastName'),
			'gender' => substr($request->input('gender'), 0, 1)
		);

		$person = Person::create($person);

		$member = new Member(
			array(
				'code' => $request->input('memberCode'),
				'date_of_registration' => $request->input('dateOfRegistration')
			)
		);
		$person->member()->save($member);

		$member = $person->member;
		$sponsor = Member::where('code', '=', $request->input('sponsorCode'))->first();
		if(is_object($sponsor)) {
			$member->sponsors()->attach($sponsor->code);
		}

		return Redirect::to('member/'.$member->code.'/purchase-order');
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
		//
	}

	public function generateMemberCode() {
		$a = rand(1, 9).''.rand(0, 9).''.rand(0, 9).''.rand(0,9);
		$date = new DateTime();
		$b = substr($date->getTimestamp(), 6);
		echo $a . '-' . $b;
	}

	public function membersJSON() {
		$members = Member::with('person')->with('sponsors.person')->get();
		return Response::json($members);
	}


	public function isUniqueMemberCode(Request $request) {
		$validator = Validator::make(
			array('code' => $request->input('code')),
			array('code' => array('required', 'unique:member'))
		);
		echo !$validator->fails();
	}

	public function downlines($memberCode) {
		$member = Member::find($memberCode);
		return view('members.downlines', array('member' => $member));
	}
	public function downlinesJSON($memberCode) {
		$member = Member::find($memberCode);
		if(!is_object($member)) return Redirect::route('home');

		$data = array();
		array_push($data, $member);

		$tree = array();
		$level = 0;

		while(count($data)>0) {
			// initialize storage per level
			$tree[$level] = array();
			foreach($data as $x) {
				// fetch information
				$detail = array();
				$detail['code'] = $x->code;
				$detail['firstName'] = $x->person->firstName;
				$detail['middleName'] = $x->person->middleName;
				$detail['lastName'] = $x->person->lastName;
				$detail['gender'] = $x->person->gender;

				// push fetched information
				array_push($tree[$level], $detail);

				// check current member x if it has y downlines
				if($x->hasDownlines()) {
					foreach($x->downlines as $y) {
						array_push($data, $y);
					}
				}

				// remove current member x
				array_shift($data);
			}
			$level++;
		}

		// remove level 0 or the root (downlines only)
		array_shift($tree);

		return Response::json($tree);
	}

	public function purchaseOrder($memberCode) {
		$member = Member::find($memberCode);
		return view('members.purchase-order', array('member' => $member));
	}

	public function addPurchaseOrder($memberCode) {
		$member = Member::find($memberCode);
		$purchaseOrderDate = $request->input('purchaseOrderDate');

		// deny adding purchase order if payout was already created
		$payout = Payout::where('payout_from', '<=', $purchaseOrderDate)
			->where('payout_to', '>=', $purchaseOrderDate)->first();

		if(is_object($payout)) { App::abort(412); }

		$purchaseOrderCode = $this->generatePurchaseOrderCode();
		$purchaseOrder = new PurchaseOrder(array('code'=> $purchaseOrderCode, 'purchase_order_date' => $purchaseOrderDate));
		$member->purchaseOrders()->save($purchaseOrder);

		// fetch newly created purchase order
		$purchaseOrder = PurchaseOrder::find($purchaseOrderCode);

		// attach products to the newly created purchase order
		$purchaseOrderProducts = $request->input('purchaseOrderProducts');
		foreach($purchaseOrderProducts as $purchaseOrderProduct) {
			$productCode = $purchaseOrderProduct[0];
			$productPrice = $purchaseOrderProduct[1];
			$quantity = $purchaseOrderProduct[2];

			$purchaseOrder->products()->attach($productCode, array('price' => $productPrice, 'quantity' => $quantity));
		}
	}

	public function purchaseOrders($memberCode) {
		$member = Member::find($memberCode);
		return view('members.purchase-orders', array('member' => $member));
	}

	public function purchaseOrdersJSON($memberCode) {
		//$member = Member::find($memberCode);
		$purchaseOrders = PurchaseOrder::where('member_code', '=', $memberCode)->orderBy('purchase_order_date', 'desc')->get(); //$member->purchaseOrders;
		return Response::json($purchaseOrders);
	}

	public function purchaseOrderProductsJSON($purchaseOrderCode) {
		$purchaseOrder = PurchaseOrder::find($purchaseOrderCode);
		$purchaseOrderProducts = $purchaseOrder->products;
		return Response::json($purchaseOrderProducts);
	}

	private function generatePurchaseOrderCode() {
		$timestamp = mktime(date('H'),date('i'),date('s'),date('m'),date('d'),date('Y'));
		$randomCode = rand(1000, 9999);
		return 'PO-'.$timestamp.'-'.$randomCode;
	}
}
