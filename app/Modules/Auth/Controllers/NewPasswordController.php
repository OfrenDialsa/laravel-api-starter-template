<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\ResetPasswordRequest;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class NewPasswordController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @OA\Post(
     * path="/api/auth/reset-password",
     * summary="Reset Password",
     * description="Update the user's password using a valid password reset token.",
     * tags={"Authentication"},
     * @OA\RequestBody(
     * required=true,
     * description="User credentials and reset token",
     * @OA\JsonContent(
     * required={"token", "email", "password", "password_confirmation"},
     * @OA\Property(property="token", type="string", example="758c087642f7..."),
     * @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="newpassword123"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword123")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Password reset successful",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="passwords.reset")
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation error or invalid token",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="The email field is required."),
     * @OA\Property(property="errors", type="object")
     * )
     * )
     * )
     */
    public function store(ResetPasswordRequest $request): JsonResponse
    {
        $status = $this->authService->resetPassword($request->validated());

        if ($status != Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return response()->json(['status' => __($status)]);
    }
}