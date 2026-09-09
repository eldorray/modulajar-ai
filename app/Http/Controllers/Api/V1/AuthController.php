<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use App\Support\ApiErrorCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $user = User::query()->where('email', mb_strtolower($credentials['email']))->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau kata sandi tidak sesuai.'],
            ])->errorBag(ApiErrorCode::InvalidCredentials->value);
        }

        $token = $user->createToken($credentials['device_name'])->plainTextToken;

        return api_response()->success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => UserResource::make($user)->resolve($request),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return api_response()->success(UserResource::make($request->user()));
    }

    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->noContent();
    }
}
