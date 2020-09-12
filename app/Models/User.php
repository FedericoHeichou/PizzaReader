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
        'role_id', 'name', 'email', 'password',
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

    public function canEdit($comic_id): bool {
        return $this->hasPermission('manager') || ($this->hasPermission('editor') && (bool)$this->comics()->find($comic_id)->first());
    }
}
