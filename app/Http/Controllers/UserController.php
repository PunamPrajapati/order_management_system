<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->userService->register($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'data' => $user,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $response = $this->userService->login($request->validated());

        return response()->json([
            'status' => $response['status'],
            'message' => $response['message'],
            'data' => $response['data'] ?? []
        ], $response['code']);
    }

    public function logout(Request $request)
    {
        $response = $this->userService->logout($request->user());

        return response()->json($response, 200);
    }
}
