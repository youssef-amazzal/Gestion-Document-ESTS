<?php

namespace App\Policies;

use App\Traits\AccessTrait;
use Illuminate\Auth\Access\HandlesAuthorization;

class FolderPolicy
{
    use HandlesAuthorization;
    use AccessTrait; // this trait contains the view, edit and upload methods
}
