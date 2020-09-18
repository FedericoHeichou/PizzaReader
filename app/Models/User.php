<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id', 'name', 'email', 'password', 'last_login', 'timezone',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'role_id' => 'integer',
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
    ];

    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function hasPermission($roleName): bool {
        $role = Role::where('name', $roleName)->first();
        return ($this->role_id <= $role->id);
    }

    public function comics() {
        return $this->belongsToMany(Comic::class);
    }

    public function comicsMinimal() {
        return $this->comics()->select('comics.name');
    }

    public function canAccess($comic_id, $role) {
        return $this->hasPermission('manager') || ($this->hasPermission($role) && (bool)$this->comics()->where('comic_id', $comic_id)->first());
    }

    public function canSee($comic_id): bool {
        return $this->canAccess($comic_id, 'checker');
    }

    public function canEdit($comic_id): bool {
        return $this->canAccess($comic_id, 'editor');
    }
}
