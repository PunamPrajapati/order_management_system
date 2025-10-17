<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function getOrder($id)
    {
        try {
            $order = Order::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return [
                'status' => false,
                'message' => 'Order not found.',
                'code' => 404
            ];
        }
        return $order;
    }

    public function getAllOrders($query, $user)
    {
        $page = $query['page'] ?? 1;
        $perPage = $query['perPage'] ?? 10;
        $orderStatus = $query['status'] ?? 'all'; 
        $queryBuilder = $user->orders()->latest();

        // Apply status filter only if it's not 'all'
        if ($orderStatus !== 'all') {
            $queryBuilder->where('order_status', $orderStatus);
        }
        $orders = $queryBuilder->paginate($perPage, ['*'], 'page', $page);
        return $orders;
    }

    public function getOrderDetail($id, $user)
    {
        // Ensure the order belongs to the logged-in user
        $order = $this->getOrder($id);
        if (isset($order['status']) && $order['status'] == false) {
            return $order;
        }
        $res = $this->checkUserOrder($order, $user);
        if($res !== true) { return $res; }
        return [
            'status' => true,
            'message' => 'Orders details retrieved successfully',
            'code' => 200,
            'data' => $order
        ];
    }

    public function createOrder(array $data, $user)
    {
        if ($data['customer_name'] == '') {
            $data['customer_name'] = $user->name;
        }
        $data['user_id'] = $user->id;
        $data['order_status'] = $data['order_status'] ?? 'pending';
        $order = DB::transaction(function () use ($data) {
            $total = collect($data['order_items'])->sum(fn($item) => $item['quantity'] * $item['price']);
            $data['total_amount'] = $total;
            return Order::create($data);
        });
        return [
            'status' => true,
            'message' => 'Order created successfully',
            'code' => 200,
            'data' => $order
        ];
    }

    public function checkUserOrder($order, $user)
    {
        // Ensure the order belongs to the logged-in user
        if ($order->user_id !== $user->id) {
            return [
                'status' => false,
                'message' => 'You do not have permission to perform any action to this order.',
                'code' => 403
            ];
        }
        return true;
    }

    public function checkOrderComplete($order)
    {
        // Ensure the order is completed
        if ($order->order_status === 'completed') {
            return [
                'status' => false,
                'message' => 'Order already completed! No action can be taken.',
                'code'  => 403,
            ];
        }
        return true;
    }

    public function updateOrder($id, array $data, $user)
    {
        $order = $this->getOrder($id);
        if (isset($order['status']) && $order['status'] == false) {
            return $order;
        }
        $res = $this->checkUserOrder($order, $user);
        if($res !== true) { return $res; }
        
        $res = $this->checkOrderComplete($order);
        if($res !== true) { return $res; }

        if ($data['customer_name'] == '') {
            $data['customer_name'] = $user->name;
        }
        $totalAmount = DB::transaction(function () use ($data) {
            $total = collect($data['order_items'])->sum(fn($item) => $item['quantity'] * $item['price']);
            return $total;
        });
        $data['total_amount'] = $totalAmount;
        $order->update($data);
        return [
            'status' => true,
            'message' => 'Order updated successfully',
            'code' => 200,
            'data' => $order
        ];
    }

    public function updateStatus($id, array $data, $user)
    {
        $newStatus = $data['status'];
        $order = $this->getOrder($id);
        if (isset($order['status']) && $order['status'] == false) {
            return $order;
        }
        $res = $this->checkUserOrder($order, $user);
        if($res !== true) { return $res; }
        
        $res = $this->checkOrderComplete($order);
        if($res !== true) { return $res; }

        if ($order->order_status === 'pending' && $newStatus === 'cancelled') {
            $order->update(['order_status' => 'cancelled']);
        } elseif ($order->order_status === 'pending' && $newStatus === 'processing') {
            $order->update(['order_status' => 'processing']);
        } elseif ($order->order_status === 'processing' && $newStatus === 'cancelled') {
            $order->update(['order_status' => 'cancelled']);
        } elseif ($order->order_status === 'processing' && $newStatus === 'completed') {
            $order->update(['order_status' => 'completed']);
        } else{
            return [
                'status' => false,
                'message' => 'Cannot update order status! Order is '. $order->order_status,
                'code'  => 403,
            ];
        }

        return [
            'status' => true,
            'message' => 'Order status updated to ' . $newStatus,
            'code' => 200,
            'data' => $order
        ];
    }

    public function deleteOrder($id, $user)
    {
        $order = $this->getOrder($id);
        if (isset($order['status']) && $order['status'] == false) {
            return $order;
        }
        $res = $this->checkUserOrder($order, $user);
        if($res !== true) { return $res; }
        
        $res = $this->checkOrderComplete($order);
        if($res !== true) { return $res; }

        $order->delete();
        return [
            'status' => true,
            'message' => 'Order deleted successfully',
            'code' => 200
        ];
    }
}
