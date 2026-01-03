<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Services\AuthService;

class RegisteredUserController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @OA\Post(
     * path="/api/auth/register",
     * summary="Register a new user",
     * description="Creates a new user account and returns the user object with an access token.",
     * tags={"Authentication"},
     * @OA\RequestBody(
     * required=true,
     * description="User registration data",
     * @OA\JsonContent(
     * required={"name", "email", "password", "password_confirmation"},
     * @OA\Property(property="name", type="string", example="John Doe"),
     * @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="password123"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="User registered successfully",
     * @OA\JsonContent(
     * @OA\Property(property="user", type="object"),
     * @OA\Property(property="token", type="string", example="1|AbCdEfG12345...")
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation error",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="The email has already been taken."),
     * @OA\Property(property="errors", type="object")
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal server error",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Gagal Register"),
     * @OA\Property(property="error", type="object")
     * )
     * )
     * )
     */
    public function store(RegisterRequest $request)
    {
        try {
            $result = $this->authService->register($request->validated());
            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json(['message' => "Gagal Register", 'error' => $e->getMessage()], 500);
        }
    }
}