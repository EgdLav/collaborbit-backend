<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasApiTokens, HasFactory, Notifiable, MustVerifyEmail;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class, 'user_workspace');
    }
    public function ownWorkspaces(): HasMany
    {
        return $this->hasMany(Workspace::class, 'owner_id');
    }
    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class, 'invitee_id');
    }
    public function tasks_executed(): HasMany
    {
        return $this->hasMany(Task::class, 'executor_id');
    }
    public function tasks_created(): HasMany
    {
        return $this->hasMany(Task::class, 'creator_id');
    }
    public function chats(): BelongsToMany
    {
        return $this->belongsToMany(Chat::class, 'chat_user');
    }
    protected $fillable = [
        'first_name',
        'last_name',
        'avatar',
        'email',
        'email_token',
        'password',
        'department',
        'bio',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function getAvatarUrlAttribute()
    {
        if ($this->avatar == 'images/default.svg') {
            return asset('images/default.svg');
        }
        return url(Storage::url($this->avatar));
    }
}
