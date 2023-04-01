<?php

namespace App\Traits;

use App\Models\File;
use App\Models\Folder;
use App\Models\Space;
use App\Models\User;

trait PathTrait {

    private function formatName(string $path): string
    {
        return str_replace(' ', '_', $path);
    }
    private function pathFromTarget(File|Folder $target): string
    {
        $space = $target->space()->first();
        $owner = $target->owner()->first();
        $root = 'uploads';
        return  $root
                . '/' . $owner->id . '_' . $this->formatName($owner->last_name . '_' . $owner->first_name)
                . '/' . $space->id . '_' . $this->formatName($space->name);
    }

    private function pathFromTargetData(User $owner, Space $space): string
    {
        $root = 'uploads';
        return  $root
                . '/' . $owner->id . '_' . $this->formatName($owner->last_name . '_' . $owner->first_name)
                . '/' . $space->id . '_' . $this->formatName($space->name);
    }

    private function constructShortPath(File|Folder $target): string
    {
        $path = $target->name;
        $parent = $target->parentFolder;
        $space = $target->space;
        while ($parent !== null) {
            $path = $parent->name . '/' . $path;
            $parent = $parent->parentFolder;
        }

        return $space->id . '_' . $this->formatName($space->name) . '/' . $path;
    }

}
