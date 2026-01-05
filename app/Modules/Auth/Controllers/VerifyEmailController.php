<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/auth/verify-email/{id}/{hash}",
     * summary="Verify User Email",
     * description="Mark the authenticated user's email address as verified using the ID and Hash from the signed URL.",
     * tags={"Authentication"},
     * security={{"apiAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="The ID of the user",
     * required=true,
     * @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     * name="hash",
     * in="path",
     * description="The verification hash",
     * required=true,
     * @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     * name="expires",
     * in="query",
     * description="The expiration timestamp of the signed URL",
     * required=true,
     * @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     * name="signature",
     * in="query",
     * description="The HMAC signature for URL security",
     * required=true,
     * @OA\Schema(type="string")
     * ),
     * @OA\Response(
     * response=302,
     * description="Redirects to the frontend dashboard with verified status"
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated"
     * ),
     * @OA\Response(
     * response=403,
     * description="Invalid or expired signature"
     * )
     * )
     */
    public function __invoke(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified'
            ]);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return response()->json([
            'message' => 'Email verified successfully'
        ]);
    }
}
