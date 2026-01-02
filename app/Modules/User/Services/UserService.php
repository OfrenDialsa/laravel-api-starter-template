<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\User;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    /**
     * Get user by id
     */
    public function findById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    /**
     * Get all users
     */
    public function all()
    {
        return $this->userRepository->all();
    }

    /**
     * Update user
     */
    public function update(int $id, array $data): ?User
    {
        $user = $this->userRepository->findById($id);

        if (! $user) {
            return null;
        }

        return DB::transaction(function () use ($user, $data) {
            return $this->userRepository->update($user, $data);
        });
    }

    /**
     * Delete user
     */
    public function delete(int $id): bool
    {
        $user = $this->userRepository->findById($id);

        if (! $user) {
            return false;
        }

        return DB::transaction(function () use ($user) {
            return $this->userRepository->delete($user);
        });
    }
}
