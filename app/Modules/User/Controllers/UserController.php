<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Requests\UserRequest;
use App\Modules\User\Services\UserService;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    public function index()
    {
        $users = $this->userService->all();

        return response()->json($users);
    }

    public function update(int $id, UserRequest $request)
    {
        $result = $this->userService->update($id, $request->validated());

        if (!$result) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($result);
    }

    public function show(int $id)
    {
        $user = $this->userService->findById($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }
}
