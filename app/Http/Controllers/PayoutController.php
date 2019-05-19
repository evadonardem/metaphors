<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Payout;

class PayoutController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('payouts.master-list');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $payout_from = $request->input('payout_from');
        $payout_to = $request->input('payout_to');
        $payout = Payout::create(array('payout_from' => $payout_from, 'payout_to' => $payout_to));
    }

    public function payoutsJSON()
    {
        $payouts = Payout::orderBy('payout_from', 'desc')->orderBy('payout_to', 'desc')->get();
        return response()->json($payouts);
    }

    public function payoutJSON($payoutFrom, $payoutTo)
    {
        // STEP 1: fetch members with purchases base on the payout period
        $members = Member::with(array('purchaseOrders' => function ($query) use ($payoutFrom, $payoutTo) {
            $query->where('purchase_order_date', '>=', $payoutFrom)
            ->where('purchase_order_date', '<=', $payoutTo);
        }))->get();

        // STEP 2: filter members from STEP 1 with at least 10 pieces total purchases
        $memberWithPurchases = array();
        foreach ($members as $member) {
            if (count($member->purchaseOrders)==0) {
                continue;
            }

            $totalQuantity = 0;
            foreach ($member->purchaseOrders as $purchaseOrder) {
                if ($purchaseOrder->purchase_order_date >= $payoutFrom && $purchaseOrder->purchase_order_date <= $payoutTo) {
                    foreach ($purchaseOrder->products as $purchaseOrderProduct) {
                        $totalQuantity += $purchaseOrderProduct->pivot->quantity;
                    }
                }
            }

            if ($totalQuantity<10) {
                continue;
            }

            array_push($memberWithPurchases, $member);
        }

        // STEP 3: initialize forest and override commission constants
        $forest = array();
        // $overrideCommission = array(5,3,2,1,1);
        $overrideCommission = array(0.20, 0.08, 0.04, 0.05, 0.03);

        // STEP 4: populate the forest
        foreach ($memberWithPurchases as $member) {
            /* if the qualified member has downlines
            then populate tree to determine the override commission
            per level, otherwise NO override commission */
            if ($member->hasDownlines()) {
                // initialize data to hold queue of members in a tree
                $data = array();

                // push or insert the root or the parent member in the queue
                array_push($data, $member);

                // initialize tree at level 0
                $tree = array();
                $level = 0;

                /* traverse the tree starting from the root or parent
                or level 0 until the last level possible (max level: 5) */
                while (count($data)>0) {
                    // initialize storage per level
                    $tree[$level] = array();

                    /* process members in the data queue
                    base on the current level  */
                    foreach ($data as $x) {

                        // fetch member x necessary information
                        $detail = array();
                        $detail['code'] = $x->code;
                        $detail['firstName'] = $x->person->firstName;
                        $detail['middleName'] = $x->person->middleName;
                        $detail['lastName'] = $x->person->lastName;
                        $detail['gender'] = $x->person->gender;

                        $purchaseOrders = $x->purchaseOrders;
                        $count = 0;
                        $sales = 0;
                        foreach ($purchaseOrders as $purchaseOrder) {
                            if ($purchaseOrder->purchase_order_date >= $payoutFrom && $purchaseOrder->purchase_order_date <= $payoutTo) {
                                foreach ($purchaseOrder->products as $purchaseOrderProduct) {
                                    $count += $purchaseOrderProduct->pivot->quantity;
                                    $sales += $purchaseOrderProduct->pivot->price * $purchaseOrderProduct->pivot->quantity;
                                }
                            }
                        }

                        $detail['quantity'] = $count;
                        $detail['sales'] = $sales;

                        if ($level == 0) {
                            $detail['totalQuantityLevel1'] = 0;
                            $detail['totalSalesLevel1'] = 0;
                            $detail['totalOverrideCommissionLevel1'] = 0;
                            $detail['totalQuantityLevel2'] = 0;
                            $detail['totalSalesLevel2'] = 0;
                            $detail['totalOverrideCommissionLevel2'] = 0;
                            $detail['totalQuantityLevel3'] = 0;
                            $detail['totalSalesLevel3'] = 0;
                            $detail['totalOverrideCommissionLevel3'] = 0;
                            $detail['totalQuantityLevel4'] = 0;
                            $detail['totalSalesLevel4'] = 0;
                            $detail['totalOverrideCommissionLevel4'] = 0;
                            $detail['totalQuantityLevel5'] = 0;
                            $detail['totalSalesLevel5'] = 0;
                            $detail['totalOverrideCommissionLevel5'] = 0;
                            $detail['totalOverrideCommission'] = 0;
                        } else {
                            //$detail['overrideCommission'] = $count * $overrideCommission[$level-1];
                            $detail['overrideCommission'] = $sales * $overrideCommission[$level-1];

                            $tree[0][0]['totalQuantityLevel'.$level] += $count;
                            $tree[0][0]['totalSalesLevel'.$level] += $sales;
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
                        if ($x->hasDownlines()) {
                            foreach ($x->downlines as $y) {
                                array_push($data, $y);
                            }
                        }

                        /* dequeue or delete processed
                        member x from the data queue */
                        array_shift($data);
                    }

                    // increment or proceed to the next level
                    $level++;
                    if ($level>5) {
                        break;
                    }
                }

                array_push($forest, array('tree' => $tree));
            }
        }

        return response()->json($forest);
    }
}
