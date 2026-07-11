<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Http\Responses\ApiResponse;
use App\Models\Chat;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->user()
            ->chats()
            ->with([
                'users',
                'lastMessage',
                'lastMessage.user',
                'workspace',
            ])
            ->withMax('messages', 'created_at')
            ->orderByDesc('messages_max_created_at');

        if ($search = $request->query('search')) {

            $query->where(function ($q) use ($search) {

                // private chats → search users
                $q->whereHas('users', function ($userQuery) use ($search) {

                    $userQuery
                        ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");

                })

                    // workspace chats → search workspace name
                    ->orWhereHas('workspace', function ($workspaceQuery) use ($search) {

                        $workspaceQuery
                            ->where('name', 'like', "%{$search}%");

                    });
            });
        }
        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }

        $chats = $query->paginate($request->query('limit', 10));

        return ApiResponse::success(data: [
            'chats' => ChatResource::collection($chats),
            'pagination' => [
                'total' => $chats->total(),
                'per_page' => $chats->perPage(),
                'current_page' => $chats->currentPage(),
                'last_page' => $chats->lastPage(),
            ],
        ]);
    }
    public function byWorkspace(Request $request, Workspace $workspace)
    {
        $this->authorize('view', $workspace);
        $chat = $workspace->chat;
        $chat->load([
            'users',
            'lastMessage',
        ])->loadCount('messages');

        return ApiResponse::success(data: [
            'chat' => new ChatResource($chat),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, User $user)
    {
        $authId = auth()->id();

        $chat = Chat::where('type', 'private')
            ->whereHas('users', fn($q) => $q->where('users.id', $authId))
            ->whereHas('users', fn($q) => $q->where('users.id', $user->id))
            ->first();

        if ($chat) {
            return ApiResponse::success(data: [
                'chat' => new ChatResource($chat),
            ]);
        }

        // create new
        $chat = Chat::create([
            'type' => 'private',
        ]);
        $chat->users()->attach([$authId, $user->id]);

        $chat->refresh();
        $chat->load([
            'users',
            'lastMessage',
        ]);

        return ApiResponse::success(data: [
            'chat' => new ChatResource($chat),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Chat $chat)
    {
        $this->authorize('view', $chat);
        $chat->load([
            'users',
            'lastMessage',
        ])->loadCount('messages');

        return ApiResponse::success(data: [
            'chat' => new ChatResource($chat),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Chat $chat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Chat $chat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chat $chat)
    {
        //
    }
}
