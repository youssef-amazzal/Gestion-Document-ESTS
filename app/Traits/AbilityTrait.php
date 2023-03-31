<?php

namespace App\Traits;

use App\Enums\Privileges;
use App\Models\File;
use App\Models\Folder;
use App\Models\Space;
use App\Models\User;

trait AbilityTrait {



    private function can(User $user, Privileges $privilege, Folder|File|Space|null $target=null): bool
    {
        if ($target !== null) {
            $hasAbility = $user->id === $target->owner->id;
            $hasAbility = $hasAbility || $user->privileges()
                                                ->where('target_id', $target->id)
                                                ->where('target_type', $target::class)
                                                ->where('action', $privilege)
                                                ->count() > 0;
        }
        else {
            $hasAbility = $user->privileges()
                                 ->where('action', $privilege)
                                 ->count() > 0;
        }

        return $hasAbility || $this->hisGroupCan($user, $privilege, $target);
    }
    private function hisGroupCan(User $user, Privileges $privilege, Folder|File|Space|null $target): bool
    {
        if ($target !== null) {
            $hasAbility = $user->groups()->whereHas('privileges', function ($query) use ($privilege, $target) {
                                $query->where('target_id', $target->id)
                                      ->where('target_type', $target::class)
                                      ->where('action', $privilege);
                            })->count() > 0;
        }
        else {
            $hasAbility = $user->groups()->whereHas('privileges', function ($query) use ($privilege, $target) {
                        $query->where('action', $privilege);
                    })->count() > 0;
        }
        return $hasAbility;
    }
}
