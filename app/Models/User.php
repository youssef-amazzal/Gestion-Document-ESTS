<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Roles;
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
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'email_verified_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => Roles::class,
    ];

    public function files() {
        return $this->hasMany(File::class, 'owner_id');
    }

    public function spaces() {
        return $this->hasMany(Space::class, 'owner_id');
    }

    public function tags() {
        return $this->hasMany(Tag::class);
    }

    public function folders() {
        return $this->hasMany(Folder::class, 'owner_id');
    }

    public function operations() {
        return $this->hasMany(Operation::class);
    }

    public function groups() {
        return $this->belongsToMany(Group::class);
    }

    public function privileges() {
        return $this->morphMany(Privilege::class, 'grantee');
    }

    public function createdGroups() {
        return $this->hasMany(Group::class);
    }

    public function filieres() {
        return $this->belongsToMany(Filiere::class);
    }

}
