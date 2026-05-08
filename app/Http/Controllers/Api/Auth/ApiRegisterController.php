<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Services\Auth\RegisterUserService;
use Illuminate\Http\JsonResponse;

class ApiRegisterController extends Controller
{
    public function __invoke(RegisterUserRequest $request, RegisterUserService $registerUserService): JsonResponse
    {
        $user = $registerUserService->register($request->validated());

        return response()->json([
            'message' => 'User registered successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ], 201);
    }
}
