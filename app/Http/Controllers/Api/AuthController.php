<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends ApiController
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'min:3'],
            'password' => ['required', 'string', 'min:3'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::query()
            ->where('username', $credentials['username'])
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        $token = $user->createToken($credentials['device_name'] ?? 'mobile-app')->plainTextToken;

        return $this->success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => UserResource::make($user),
        ], 'Login berhasil.');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success(UserResource::make($request->user()));
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        /** @var PersonalAccessToken|null $token */
        $token = $user?->currentAccessToken();

        if ($token !== null) {
            $token->delete();
        }

        return $this->success(message: 'Logout berhasil.');
    }
}
