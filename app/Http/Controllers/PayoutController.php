<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PayoutController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return View::make('payouts.master-list');
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
		$payout_from = Input::get('payout_from');
		$payout_to = Input::get('payout_to');
		$payout = Payout::create(array('payout_from' => $payout_from, 'payout_to' => $payout_to));
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

	public function payoutsJSON() {
		$payouts = Payout::orderBy('payout_from', 'desc')->orderBy('payout_to', 'desc')->get();
		return Response::json($payouts);
	}

	public function payoutJSON($payoutFrom, $payoutTo) {
		// STEP 1: fetch members with purchases base on the payout period
		$members = Member::with(array('purchaseOrders' => function($query) use ($payoutFrom, $payoutTo) {
			$query->where('purchase_order_date', '>=', $payoutFrom)
			->where('purchase_order_date', '<=', $payoutTo);
		}))->get();

		// STEP 2: filter members from STEP 1 with at least 10 pieces total purchases
		$memberWithPurchases = array();
		foreach($members as $member) {
			if(count($member->purchaseOrders)==0) continue;

			$totalQuantity = 0;
			foreach($member->purchaseOrders as $purchaseOrder) {
				if($purchaseOrder->purchase_order_date >= $payoutFrom && $purchaseOrder->purchase_order_date <= $payoutTo) {
					foreach($purchaseOrder->products as $purchaseOrderProduct) {
						$totalQuantity += $purchaseOrderProduct->pivot->quantity;
					}
				}
			}

			if($totalQuantity<10) continue;

			array_push($memberWithPurchases, $member);
		}

		// STEP 3: initialize forest and override commission constants
		$forest = array();
		$overrideCommission = array(5,3,2,1,1);

		// STEP 4: populate the forest
		foreach($memberWithPurchases as $member) {
			/* if the qualified member has downlines
			then populate tree to determine the override commission
			per level, otherwise NO override commission */
			if($member->hasDownlines()) {
				// initialize data to hold queue of members in a tree
				$data = array();

				// push or insert the root or the parent member in the queue
				array_push($data, $member);

				// initialize tree at level 0
				$tree = array();
				$level = 0;

				/* traverse the tree starting from the root or parent
				or level 0 until the last level possible (max level: 5) */
				while(count($data)>0) {
					// initialize storage per level
					$tree[$level] = array();

					/* process members in the data queue
					base on the current level  */
					foreach($data as $x) {

						// fetch member x necessary information
						$detail = array();
						$detail['code'] = $x->code;
						$detail['firstName'] = $x->person->firstName;
						$detail['middleName'] = $x->person->middleName;
						$detail['lastName'] = $x->person->lastName;
						$detail['gender'] = $x->person->gender;

						$purchaseOrders = $x->purchaseOrders;
						$count = 0;
						foreach($purchaseOrders as $purchaseOrder) {
							if($purchaseOrder->purchase_order_date >= $payoutFrom && $purchaseOrder->purchase_order_date <= $payoutTo) {
								foreach($purchaseOrder->products as $purchaseOrderProduct) {
									$count += $purchaseOrderProduct->pivot->quantity;
								}
							}
						}

						$detail['quantity'] = $count;

						if($level == 0) {
							$detail['totalQuantityLevel1'] = 0;
							$detail['totalOverrideCommissionLevel1'] = 0;
							$detail['totalQuantityLevel2'] = 0;
							$detail['totalOverrideCommissionLevel2'] = 0;
							$detail['totalQuantityLevel3'] = 0;
							$detail['totalOverrideCommissionLevel3'] = 0;
							$detail['totalQuantityLevel4'] = 0;
							$detail['totalOverrideCommissionLevel4'] = 0;
							$detail['totalQuantityLevel5'] = 0;
							$detail['totalOverrideCommissionLevel5'] = 0;
							$detail['totalOverrideCommission'] = 0;
						} else {
							$detail['overrideCommission'] = $count * $overrideCommission[$level-1];

							$tree[0][0]['totalQuantityLevel'.$level] += $count;
							$tree[0][0]['totalOverrideCommissionLevel'.$level] += $detail['overrideCommission'];
							$tree[0][0]['totalOverrideCommission'] += $detail['overrideCommission'];
						}


						/* push or insert member x fetched information
						in the current level */
						array_push($tree[$level], $detail);

						/* if member x has downlines, then enqueue
						members or downlines in the data queue.
						NOTE: enqueued members or downlines will be
						processed for the next level */
						if($x->hasDownlines()) {
							foreach($x->downlines as $y) {
								array_push($data, $y);
							}
						}

						/* dequeue or delete processed
						member x from the data queue */
						array_shift($data);
					}

					// increment or proceed to the next level
					$level++;
					if($level>5) break;
				}

				array_push($forest, array('tree' => $tree));
			}
		}

		return Response::json($forest);
	}

}
