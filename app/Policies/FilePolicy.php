<?php

namespace App\Policies;

use App\Traits\AccessTrait;
use Illuminate\Auth\Access\HandlesAuthorization;

class FilePolicy
{
    use HandlesAuthorization;
    use AccessTrait; // this trait contains the view, edit and upload methods
}
