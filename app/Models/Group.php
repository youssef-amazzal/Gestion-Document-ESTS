<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Class File
 * @mixin QueryBuilder
 * @mixin EloquentBuilder
 */
class Group extends Model
{
    use HasFactory;

    public function memebers(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function privileges(): MorphMany
    {
        return $this->morphMany(Privilege::class, 'grantee');
    }
}
