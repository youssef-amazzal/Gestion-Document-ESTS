<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Class File
 *
 * @mixin QueryBuilder
 * @mixin EloquentBuilder
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Privilege> $privileges
 * @property-read int|null $privileges_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\GroupFactory factory($count = null, $state = [])
 * @method static EloquentBuilder|Group newModelQuery()
 * @method static EloquentBuilder|Group newQuery()
 * @method static EloquentBuilder|Group query()
 * @method static EloquentBuilder|Group whereCreatedAt($value)
 * @method static EloquentBuilder|Group whereId($value)
 * @method static EloquentBuilder|Group whereName($value)
 * @method static EloquentBuilder|Group whereUpdatedAt($value)
 * @method static EloquentBuilder|Group whereUserId($value)
 * @mixin \Eloquent
 */
class Group extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function privileges(): MorphMany
    {
        return $this->morphMany(Privilege::class, 'grantee');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
