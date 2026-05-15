<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\TaskResource;
use App\Http\Resources\UserPrivateInfoResource;
use App\Http\Resources\UserPrivateResource;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    public function update(UserUpdateRequest $request)
    {
        $updatedUser = $this->userService->update(
            $request->user(),
            $request->validated()
        );
        return ApiResponse::success('Your profile successfully updated', 200, [
            'user' => new UserResource($updatedUser->loadCount([
                'tasks_created',
                'tasks_executed',
                'workspaces',
                'chats',
            ])),
        ]);
    }

    public function destroy(Request $request)
    {
        $this->userService->delete($request->user());

        return ApiResponse::success();
    }
    public function index(Request $request) {
        $query = User::query();
        if ($dep = $request->department) {
            $query->where('users.department', $dep);
        }
        if ($name = $request->name) {
            $query->where(function ($q) use ($name) {
                $q->where('users.first_name', 'like', "%$name%")
                    ->orWhere('users.last_name', 'like', "%$name%");
            });
        }
        $users = $query->withCount([
            'tasks_created',
            'tasks_executed',
            'workspaces',
            'chats',
        ])->paginate(10);

        return ApiResponse::success(data:[
            'users' => UserResource::collection($users->items()),
            'pagination' => [
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }
    public function show(Request $request, User $user) {
        return ApiResponse::success(data:[
            'user' => new UserResource($request->user()->loadCount(['tasks_created', 'tasks_executed', 'workspaces', 'chats'])),
        ]);
    }
    public function me(Request $request) {
        return ApiResponse::success(data:[
            'user' => new UserPrivateResource($request->user()->loadCount(['tasks_created', 'tasks_executed', 'workspaces', 'chats'])),
        ]);
    }
}
