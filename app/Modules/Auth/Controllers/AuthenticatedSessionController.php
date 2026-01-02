<?php
namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Modules\User\Models\User; // Pastikan path model benar

class AuthenticatedSessionController extends Controller
{
    /**
     * Login API
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Cek user & password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Generate token dengan Sanctum
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Logout API
     */
    public function destroy(Request $request)
    {
        $user = $request->user(); // API user
        if ($user) {
            $user->tokens()->delete(); // Hapus token
        }

        return response()->json(['message' => 'Logged out']);
    }
}
