<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Requests\OrderStatusRequest;
use App\Http\Resources\OrderResource;
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
        $orders = $this->orderService->getAllOrders($request->query(), $request->user());
        return response()->json([
            'status' => true,
            'message' => 'Orders retrieved successfully',
            'data' => OrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ]
        ], 200);
    }

    public function show(Request $request, $id)
    {
        $orderResponse = $this->orderService->getOrderDetail($id, $request->user());

        return response()->json([
            'status' => $orderResponse['status'],
            'message' => $orderResponse['message'],
            'data' => !empty($orderResponse['data']) ? new OrderResource($orderResponse['data']) : []
        ], $orderResponse['code']);
    }

    public function store(OrderRequest $request)
    {
        $orderResponse = $this->orderService->createOrder($request->validated(), $request->user());
        return response()->json([
            'status' => $orderResponse['status'],
            'message' => $orderResponse['message'],
            'data' => !empty($orderResponse['data']) ? new OrderResource($orderResponse['data']) : []
        ], $orderResponse['code']);
    }
    
    public function update(OrderRequest $request, $id)
    {
        $orderResponse = $this->orderService->updateOrder($id, $request->validated(), $request->user());
        return response()->json([
            'status' => $orderResponse['status'],
            'message' => $orderResponse['message'],
            'data' => !empty($orderResponse['data']) ? new OrderResource($orderResponse['data']) : []
        ], $orderResponse['code']);
    }

    public function updateStatus(OrderStatusRequest $request, $id)
    {
        $orderResponse = $this->orderService->updateStatus($id, $request->validated(), $request->user());
        return response()->json([
            'status' => $orderResponse['status'],
            'message' => $orderResponse['message'],
            'data' => !empty($orderResponse['data']) ? new OrderResource($orderResponse['data']) : []
        ], $orderResponse['code']);
    }

    public function destroy(Request $request, $id)
    {
        $order = $this->orderService->deleteOrder($id, $request->user());
        return response()->json([
            'status' => $order['status'],
            'message' => $order['message']
        ], $order['code']);
    }
}

