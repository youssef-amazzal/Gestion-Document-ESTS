<?php

namespace App\Models;

use App\Enums\Privileges;
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

    protected static function booted()
    {
        static::created(function (Group $group) {
            if ($group->name === 'Professors') {
                foreach (Privileges::getProfessorsPrivileges() as $privilege) {
                    $group->privileges()->create([
                        'action' => $privilege,
                        'grantee_id' => $group->id,
                        'grantee_type' => Group::class,
                        'type' => Privileges::getType($privilege),
                    ]);
                }
            }
        });
    }

    protected $guarded = [];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function privileges(): MorphMany
    {
        return $this->morphMany(Privilege::class, 'grantee');
    }

    public function filePrivileges()
    {
        return $this->privileges()->select('target_id')->where('target_type', File::class)->distinct();
    }

    public function folderPrivileges()
    {
        return $this->privileges()->select('target_id')->where('target_type', Folder::class)->distinct();
    }

    public function spacePrivileges()
    {
        return $this->privileges()->select('target_id')->where('target_type', Space::class)->distinct();
    }


}
