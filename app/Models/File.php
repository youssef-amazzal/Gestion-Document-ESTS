<?php

namespace App\Models;

use App\Traits\ShareTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\Storage;

/**
 * Class File
 *
 * @mixin QueryBuilder
 * @mixin EloquentBuilder
 * @property Folder $parentFolder
 * @property Space $space
 * @property User $owner
 * @property File $originalFile
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $is_pinned
 * @property int $size
 * @property string|null $path
 * @property string|null $mime_type
 * @property int|null $parent_folder_id
 * @property int $is_shortcut
 * @property int|null $original_id
 * @property int $owner_id
 * @property int $space_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Folder> $ancestors
 * @property-read int|null $ancestors_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Operation> $operations
 * @property-read int|null $operations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Privilege> $privileges
 * @property-read int|null $privileges_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @method static \Database\Factories\FileFactory factory($count = null, $state = [])
 * @method static EloquentBuilder|File newModelQuery()
 * @method static EloquentBuilder|File newQuery()
 * @method static EloquentBuilder|File query()
 * @method static EloquentBuilder|File whereCreatedAt($value)
 * @method static EloquentBuilder|File whereDescription($value)
 * @method static EloquentBuilder|File whereId($value)
 * @method static EloquentBuilder|File whereIsPinned($value)
 * @method static EloquentBuilder|File whereIsShortcut($value)
 * @method static EloquentBuilder|File whereMimeType($value)
 * @method static EloquentBuilder|File whereName($value)
 * @method static EloquentBuilder|File whereOriginalId($value)
 * @method static EloquentBuilder|File whereOwnerId($value)
 * @method static EloquentBuilder|File whereParentFolderId($value)
 * @method static EloquentBuilder|File wherePath($value)
 * @method static EloquentBuilder|File whereSize($value)
 * @method static EloquentBuilder|File whereSpaceId($value)
 * @method static EloquentBuilder|File whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class File extends Model
{
    use HasFactory, ShareTrait;
    protected $guarded = [];
    protected $hidden = ['path', 'parentFolder', 'ancestors'];

    // When a file is updated, update the updated_at timestamp of the ancestors folders
    protected $touches = ['ancestors'];

    protected static function booted(): void
    {
        static::created(function (File $file) {
            if ($file->parentFolder) {
                $ancestors = $file->parentFolder->ancestors()->get();
                $ancestors->push($file->parentFolder);
                $file->ancestors()->attach($ancestors);
            }
        });

        static::updated(function (File $file) {
            if ($file->isDirty('parent_folder_id')) {
                $file->ancestors()->detach();
                if ($file->parentFolder) {
                    $ancestors = $file->parentFolder->ancestors()->get();
                    $ancestors->push($file->parentFolder);
                    $file->ancestors()->attach($ancestors);
                }
            }
        });

        static::deleting(function (File $file) {
            var_dump($file->toArray());
            Storage::disk('local')->delete($file->path);
        });
    }

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function parentFolder() {
        return $this->belongsTo(Folder::class, 'parent_folder_id');
    }

    public function ancestors() {
        return $this->morphToMany(Folder::class, 'containable');
    }

    public function space() {
        return $this->belongsTo(Space::class, 'space_id');
    }

    public function originalFile() {
        return $this->belongsTo(Folder::class, 'original_id');
    }

    public function privileges() {
        return $this->morphMany(Privilege::class, 'target');
    }

    public function tags(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function operations() {
        return $this->morphMany(Operation::class, 'trackable');
    }
}
