<?php

namespace App\Traits;

use App\Enums\Privileges;
use App\Models\File;
use App\Models\Folder;
use App\Models\Space;
use App\Models\User;

trait AccessTrait {

    use AbilityTrait;

    public function view(User $user, File|Folder|Space $target): bool
    {
        // if this is true, then the other two will not be checked, since the space is the highest level
        $isSharedWithUser = $this->can($user, Privileges::View, $target->space);

        $isSharedWithUser = $isSharedWithUser || $this->can($user, Privileges::View, $target);

        if ($target->parentFolder !== null) {
            $isSharedWithUser = $isSharedWithUser || $user->can('view', $target->parentFolder);
        }

        return $isSharedWithUser;
    }

    public function edit(User $user, File|Folder|Space $target): bool
    {
        // if this is true, then the other two will not be checked, since the space is the highest level
        $hasFullAccess = $this->can($user, Privileges::Edit, $target->space);

        $hasFullAccess = $hasFullAccess || $this->can($user, Privileges::Edit, $target);

        if ($target->parentFolder !== null) {
            $hasFullAccess = $hasFullAccess || $user->can('edit', $target->parentFolder);
        }

        return $hasFullAccess;
    }

    public function uploadInto(User $user, Folder|Space $target): bool
    {
        // if this is true, then the other two will not be checked, since the space is the highest level
        $canUploadInto = $this->can($user, Privileges::Upload_Into, $target->space);

        $canUploadInto = $canUploadInto || $this->can($user, Privileges::Upload_Into, $target);

        if ($target->parentFolder !== null) {
            $canUploadInto = $canUploadInto || $user->can('uploadInto', $target->parentFolder);
        }

        return $canUploadInto;
    }
}

