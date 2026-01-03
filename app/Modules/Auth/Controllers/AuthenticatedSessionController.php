<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Modules\User\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/auth/login",
     * summary="User Login",
     * description="Authenticate user with email and password to receive a Sanctum token.",
     * tags={"Authentication"},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email","password"},
     * @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="password123")
     * ),
     * ),
     * @OA\Response(
     * response=200,
     * description="Login Successful",
     * @OA\JsonContent(
     * @OA\Property(property="user", type="object"),
     * @OA\Property(property="token", type="string", example="1|AbCdEfG12345...")
     * )
     * ),
     * @OA\Response(response=401, description="Invalid credentials"),
     * @OA\Response(response=422, description="Validation errors")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * @OA\Post(
     * path="/api/auth/logout",
     * summary="User Logout",
     * description="Revoke the current user's access tokens.",
     * tags={"Authentication"},
     * security={{"apiAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Successfully logged out",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Logged out")
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function destroy(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->tokens()->delete();
        }

        return response()->json(['message' => 'Logged out']);
    }
}