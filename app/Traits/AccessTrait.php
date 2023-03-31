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
        // if this is true, then the other two will not be checked
        $isSharedWithUser = $this->can($user, Privileges::View, $target);

        $isSharedWithUser = $isSharedWithUser || $this->can($user, Privileges::View, $target);

        if ($target->parentFolder !== null) {
            $isSharedWithUser = $isSharedWithUser || $user->can('view', $target->parentFolder);
        }

        return $isSharedWithUser;
    }

    public function edit(User $user, File|Folder|Space $target): bool
    {
        // if this is true, then the other two will not be checked
        $hasFullAccess = $this->can($user, Privileges::Edit, $target);

        $hasFullAccess = $hasFullAccess || $this->can($user, Privileges::Edit, $target);

        if ($target->parentFolder !== null) {
            $hasFullAccess = $hasFullAccess || $user->can('edit', $target->parentFolder);
        }

        return $hasFullAccess;
    }

    public function upload(User $user, File|Folder|Space $target): bool
    {
        // if this is true, then the other two will not be checked
        $canUploadInto = $this->can($user, Privileges::Upload, $target);

        $canUploadInto = $canUploadInto || $this->can($user, Privileges::Upload, $target);

        if ($target->parentFolder !== null) {
            $canUploadInto = $canUploadInto || $user->can('upload', $target->parentFolder);
        }

        return $canUploadInto;
    }
}

