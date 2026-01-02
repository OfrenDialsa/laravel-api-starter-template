<?php
namespace App\Modules\User\Repository;

use App\Modules\User\Models\User;

class UserRepository
{
    public function create(array $data): User
    {
        return User::create($data);
    }
}
