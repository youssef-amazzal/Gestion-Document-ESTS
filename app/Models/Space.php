<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * App\Models\Space
 *
 * @property int $id
 * @property string $name
 * @property int $is_permanent
 * @property int $owner_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $files
 * @property-read int|null $files_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Folder> $folders
 * @property-read int|null $folders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Operation> $operations
 * @property-read int|null $operations_count
 * @property-read \App\Models\User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Privilege> $privileges
 * @property-read int|null $privileges_count
 * @method static \Database\Factories\SpaceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Space newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Space newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Space query()
 * @method static \Illuminate\Database\Eloquent\Builder|Space whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Space whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Space whereIsPermanent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Space whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Space whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Space whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Space extends Model
{
    use HasFactory;

    protected static function booted()
    {
        self::deleting(function (Space $space) {
            $descendants = $space->files()->get()->pluck('path')->toArray();
            Storage::disk('local')->delete($descendants);
        });
    }

    protected $fillable = [
        'name',
        'is_permanent',
        'owner_id',
    ];

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function privileges() {
        return $this->morphMany(Privilege::class, 'target');
    }
    public function operations() {
        return $this->morphMany(Operation::class, 'trackable');
    }

    public function files() {
        return $this->hasMany(File::class, 'space_id');
    }

    public function folders() {
        return $this->hasMany(Folder::class, 'space_id');
    }
}
