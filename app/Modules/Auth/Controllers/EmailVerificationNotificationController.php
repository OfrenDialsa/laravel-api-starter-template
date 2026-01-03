<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/auth/email/verification-notification",
     * summary="Resend Verification Email",
     * description="Sends a new email verification link to the authenticated user.",
     * tags={"Authentication"},
     * security={{"apiAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Verification link sent successfully",
     * @OA\JsonContent(
     * @OA\Property(property="status", type="string", example="verification-link-sent")
     * )
     * ),
     * @OA\Response(
     * response=302,
     * description="Redirected to dashboard if email is already verified"
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated"
     * )
     * )
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended('/dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['status' => 'verification-link-sent']);
    }
}