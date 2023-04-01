<?php

namespace App\Policies;

use App\Enums\Privileges;
use App\Enums\Roles;
use App\Models\User;
use App\Traits\AbilityTrait;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;
    use AbilityTrait;

    /**
     * Determine whether the user can upload physical files in his space.
     *
     * @param User $user
     * @return bool
     */
    public function upload(User $user)
    {
        return $this->can($user, Privileges::Can_Upload);
    }

    public function isAdmin(User $user)
    {
        return $user->role === Roles::ADMIN;
    }
}
