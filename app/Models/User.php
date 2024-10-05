<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
        'last_login',
        'timezone',
        'all_comics',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'role_id' => 'integer',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login' => 'datetime',
            'all_comics' => 'boolean',
        ];
    }

    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function hasPermission($role_name): bool {
        if (!config('roles')) {
            $roles = Role::all('id', 'name')->pluck('id', 'name');
            config(['roles' => $roles]);
        }
        return ($this->role_id <= config("roles.$role_name"));
    }

    public function comics() {
        return $this->belongsToMany(Comic::class);
    }

    public function comicsMinimal() {
        return $this->comics()->select('comics.name');
    }

    public function canAccess($comic_id, $role) {
        return $this->hasPermission('manager') || ($this->hasPermission($role) &&
                ($this->all_comics || (bool)$this->comics()->where('comic_id', $comic_id)->first()));
    }

    public function canSee($comic_id): bool {
        return $this->canAccess($comic_id, 'checker');
    }

    public function canEdit($comic_id): bool {
        return $this->canAccess($comic_id, 'editor');
    }
}
