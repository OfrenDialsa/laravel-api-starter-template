<?php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Repositories\AuthRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthService
{
    protected AuthRepository $authRepo;

    public function __construct(AuthRepository $authRepo)
    {
        $this->authRepo = $authRepo;
    }

    public function register(array $data)
    {
        $user = $this->authRepo->register($data);

        event(new Registered($user));

        $token = $this->authRepo->generateToken($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(string $email, string $password)
    {
        $user = $this->authRepo->login($email, $password);

        if (!$user) {
            throw new \Exception('Invalid credentials');
        }

        $token = $this->authRepo->generateToken($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function resetPassword(array $data)
{
    return Password::reset($data, function ($user) use ($data) {
        $user->forceFill([
            'password' => Hash::make($data['password']),
            'remember_token' => Str::random(60),
        ])->save();
        event(new PasswordReset($user));
    });
}

    public function logout($user)
    {
        $this->authRepo->logout($user);
    }
}
