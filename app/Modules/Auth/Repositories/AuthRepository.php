<?php

namespace App\Modules\Auth\Repositories;

use App\Modules\User\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthRepository
{
    protected User $userModel;

    public function __construct(User $user)
    {
        $this->userModel = $user;
    }

    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    public function login(string $email, string $password): ?User
    {
        $user = User::where('email', $email)->first();
        if ($user && Hash::check($password, $user->password)) {
            return $user;
        }
        return null;
    }

    public function generateToken(User $user): string
    {
        return $user->createToken('api-token')->plainTextToken;
    }

    public function logout(User $user)
    {
        $user->currentAccessToken()->delete();
    }
}
