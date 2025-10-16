<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Requests\OrderStatusRequest;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $order = $this->orderService->getAllOrders($request->query(), $request->user());
        return $order;
        return response()->json([
            'status' => $order['status'],
            'message' => $order['message'],
            'data' => $order['data'] ?? []
        ], $order['code']);
    }

    public function show(Request $request, $id)
    {
        $order = $this->orderService->getOrderDetail($id, $request->user());
        return response()->json([
            'status' => $order['status'],
            'message' => $order['message'],
            'data' => $order['data'] ?? []
        ], $order['code']);
    }

    public function store(OrderRequest $request)
    {
        $order = $this->orderService->createOrder($request->validated(), $request->user());
        return response()->json([
            'status' => $order['status'],
            'message' => $order['message'],
            'data' => $order['data'] ?? []
        ], $order['code']);
    }
    
    public function update(OrderRequest $request, $id)
    {
        $order = $this->orderService->updateOrder($id, $request->validated(), $request->user());
        return response()->json([
            'status' => $order['status'],
            'message' => $order['message'],
            'data' => $order['data'] ?? []
        ], $order['code']);
    }

    public function updateStatus(OrderStatusRequest $request, $id)
    {
        $order = $this->orderService->updateStatus($id, $request->validated(), $request->user());
        return response()->json([
            'status' => $order['status'],
            'message' => $order['message'],
            'data' => $order['data'] ?? []
        ], $order['code']);
    }

    public function destroy(Request $request, $id)
    {
        $order = $this->orderService->deleteOrder($id, $request->user());
        return response()->json([
            'status' => $order['status'],
            'message' => $order['message'],
            'data' => $order['data'] ?? []
        ], $order['code']);
    }
}

