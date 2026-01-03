<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Requests\UserRequest;
use App\Modules\User\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    /**
     * @OA\Get(
     * path="/api/users",
     * summary="Get all users",
     * description="Retrieve a list of all registered users.",
     * tags={"User Management"},
     * security={{"apiAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(type="array", @OA\Items(type="object"))
     * ),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index()
    {
        $users = $this->userService->all();

        return response()->json($users);
    }

    /**
     * @OA\Get(
     * path="/api/users/{id}",
     * summary="Get user by ID",
     * description="Retrieve specific user details.",
     * tags={"User Management"},
     * security={{"apiAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID of the user to return",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(type="object")
     * ),
     * @OA\Response(response=404, description="User not found"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function show(int $id)
    {
        $user = $this->userService->findById($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    /**
     * @OA\Put(
     * path="/api/users/{id}",
     * summary="Update user",
     * description="Update existing user information.",
     * tags={"User Management"},
     * security={{"apiAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="ID of the user to update",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="name", type="string", example="John Doe Updated"),
     * @OA\Property(property="email", type="string", format="email", example="john.updated@example.com")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="User updated successfully",
     * @OA\JsonContent(type="object")
     * ),
     * @OA\Response(response=404, description="User not found"),
     * @OA\Response(response=422, description="Validation error"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function update(int $id, UserRequest $request)
    {
        $result = $this->userService->update($id, $request->validated());

        if (!$result) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($result);
    }

    /**
     * @OA\Get(
     * path="/api/me",
     * summary="Get current user",
     * tags={"User Management"},
     * security={{"apiAuth":{}}},
     * @OA\Response(response=200, description="Success")
     * )
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}