<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Collection;

class OrderController extends Controller
{
    public function index()
    {
        // list order with pagination
        $order = Order::paginate(10);
        return OrderResource::collection($order);
    }

    public function show($id)
    {
     //show id order
        return new OrderResource(Order::findOrFail($id));
    }

    // create function export to download file order
    public function export()
    {
        // header
        $headers = [
            "Content-Type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=orders.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];
        // callback
        $callback = function () {
            // Get orders
            $orders = Order::all();

            // Open file with write permission
            $file = fopen("php://output", "w");

            // Create header row
            fputcsv($file, ["ID", "Name", "Email", "Product Title", "Price", "Quantity"]);

            // Attach body to header row
            foreach ($orders as $order) {
                foreach ($order->order_items as $orderItem) {
                    fputcsv($file, [
                        $order->id,
                        $order->name,
                        $order->email,
                        $orderItem->product,
                        $orderItem->price,
                        $orderItem->quantity
                    ]);
                }
            }

            // Close the file after all data is written
            fclose($file);
        };

        return \Response::stream($callback, 200, $headers);
    }
}
