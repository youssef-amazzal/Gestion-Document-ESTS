<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Storage;

/**
 * Class Folder
 *
 * @mixin QueryBuilder
 * @mixin EloquentBuilder
 * @property Folder $parentFolder
 * @property Space $space
 * @property User $owner
 * @property File $originalFolder
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $size
 * @property int $is_pinned
 * @property int $owner_id
 * @property int $is_shortcut
 * @property int|null $original_id
 * @property int|null $parent_folder_id
 * @property int $space_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Folder> $ancestors
 * @property-read int|null $ancestors_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $descendantsFiles
 * @property-read int|null $descendants_files_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Folder> $descendantsFolders
 * @property-read int|null $descendants_folders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $files
 * @property-read int|null $files_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Folder> $folders
 * @property-read int|null $folders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Operation> $operations
 * @property-read int|null $operations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Privilege> $privileges
 * @property-read int|null $privileges_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @method static \Database\Factories\FolderFactory factory($count = null, $state = [])
 * @method static EloquentBuilder|Folder newModelQuery()
 * @method static EloquentBuilder|Folder newQuery()
 * @method static EloquentBuilder|Folder query()
 * @method static EloquentBuilder|Folder whereCreatedAt($value)
 * @method static EloquentBuilder|Folder whereDescription($value)
 * @method static EloquentBuilder|Folder whereId($value)
 * @method static EloquentBuilder|Folder whereIsPinned($value)
 * @method static EloquentBuilder|Folder whereIsShortcut($value)
 * @method static EloquentBuilder|Folder whereName($value)
 * @method static EloquentBuilder|Folder whereOriginalId($value)
 * @method static EloquentBuilder|Folder whereOwnerId($value)
 * @method static EloquentBuilder|Folder whereParentFolderId($value)
 * @method static EloquentBuilder|Folder whereSize($value)
 * @method static EloquentBuilder|Folder whereSpaceId($value)
 * @method static EloquentBuilder|Folder whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Folder extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $hidden = ['parentFolder', 'ancestors'];

    // When a folder is updated, update the updated_at timestamp of the parent folder
    protected $touches = ['ancestors'];

    protected static function booted(): void
    {
        static::created(function (Folder $folder) {
            if ($folder->parentFolder) {
                $ancestors = $folder->parentFolder->ancestors()->get();
                $ancestors->push($folder->parentFolder);
                $folder->ancestors()->attach($ancestors);
            }
        });

        static::updated(function (Folder $folder) {
            if ($folder->isDirty('parent_folder_id')) {
                $folder->ancestors()->detach();
                if ($folder->parentFolder) {
                    $ancestors = $folder->parentFolder->ancestors()->get();
                    $ancestors->push($folder->parentFolder);
                    $folder->ancestors()->attach($ancestors);
                }
            }
        });

        static::deleting(function (Folder $folder) {
            $descendants = $folder->descendantsFiles()->get()->pluck('path')->toArray();
            Storage::disk('local')->delete($descendants);
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

    public function descendantsFolders() {
        return $this->morphedByMany(Folder::class, 'containable');
    }

    public function descendantsFiles() {
        return $this->morphedByMany(File::class, 'containable');
    }

    public function space() {
        return $this->belongsTo(Space::class, 'space_id');
    }

    public function originalFolder() {
        return $this->belongsTo(Folder::class, 'original_id');
    }

    public function privileges() {
        return $this->morphMany(Privilege::class, 'target');
    }

    public function files() {
        return $this->hasMany(File::class, 'parent_folder_id');
    }

    public function folders() {
        return $this->hasMany(Folder::class, 'parent_folder_id');
    }

    public function tags() {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function operations() {
        return $this->morphMany(Operation::class, 'trackable');
    }
}
