<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    /**
     * Register a new user
     */
    public function register(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        return $user;
    }

    /**
     * Login and create access token
     */
    public function login(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return [
                'status' => false,
                'message' => 'The provided credentials are incorrect.',
                'code'  => 401,
            ];
        }

        // Create personal access token using Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'status' => true,
            'message' => 'User login successfully',
            'code' => 200,
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]
        ];
    }

    /**
     * Logout (revoke token)
     */
    public function logout($user)
    {
        $user->currentAccessToken()->delete();
        return ['status' => true,'message' => 'Logged out successfully'];
    }
}
