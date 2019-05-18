<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DailySalesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return View::make('reports.dailysales.index');
    }

    public function dailySalesJSON($start, $end)
    {
        $interval = new DateInterval('P1D');
        $start = Carbon\Carbon::parse($start);
        $end = Carbon\Carbon::parse($end);
        $daterange = new DatePeriod($start, $interval, $end->addDay(1));

        $dailySales = [];
        foreach ($daterange as $date) {
            $purchaseOrders = PurchaseOrder::where('purchase_order_date', '=', $date->format('Y-m-d'))->get();

            $products = Product::orderBy('title')->get();
            foreach ($products as $product) {
                $product->sales = (object) [];
                $product->sales->quantity = 0;
                $product->sales->amount = 0;

                foreach ($purchaseOrders as $purchaseOrder) {
                    $purchaseOrderProducts = $purchaseOrder->products->filter(function ($purchaseOrderProduct) use ($product) {
                        if ($purchaseOrderProduct->code == $product->code) {
                            return $purchaseOrderProduct;
                        }
                    });
                    foreach ($purchaseOrderProducts as $purchaseOrderProduct) {
                        $product->sales->quantity += $purchaseOrderProduct->pivot->quantity;
                        $product->sales->amount += $purchaseOrderProduct->pivot->quantity * $purchaseOrderProduct->pivot->price;
                    }
                }
            }

            $products = $products->filter(function ($product) {
                if ($product->sales->quantity > 0 && $product->sales->amount >0) {
                    return $product;
                }
            });

            $arr = [
                'date' => $date->format('Y M d'),
                'products' => $products->toArray()
            ];

            $dailySales[] = $arr;
        }

        $end->subDay(1);
        return ['daterange' => ($start->format('Y-m-d') == $end->format('Y-m-d')) ? $start->format('Y M d') :
            $start->format('Y M d') . ' to ' . (($start->format('Y') == $end->format('Y')) ? $end->format('M d') : $end->format('Y M d')),
            'dailySales' => $dailySales];
    }

    public function dailySalesPrint($start, $end)
    {
        set_time_limit(0);

        $pdf = PDF::loadView('pdf.dailysales', [ 'report' => $this->dailySalesJSON($start, $end) ]);
        return $pdf->stream('dailysales.pdf');
    }
}
