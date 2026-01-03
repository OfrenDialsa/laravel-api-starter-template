<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetLinkController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/auth/forgot-password",
     * summary="Send Password Reset Link",
     * description="Send a password reset link to the user's email address.",
     * tags={"Authentication"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email"},
     * @OA\Property(property="email", type="string", format="email", example="user@example.com")
     * ),
     * ),
     * @OA\Response(
     * response=200,
     * description="Reset link sent successfully",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="passwords.sent")
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation error or user not found",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="We can't find a user with that email address."),
     * @OA\Property(property="errors", type="object", example={"email": {"We can't find a user with that email address."}})
     * )
     * )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return response()->json(['status' => __($status)]);
    }
}