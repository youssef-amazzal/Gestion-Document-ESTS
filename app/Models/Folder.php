<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Storage;

/**
 * Class Folder
 * @mixin QueryBuilder
 * @mixin EloquentBuilder
 * @property Folder $parentFolder
 * @property Space $space
 * @property User $owner
 * @property File $originalFolder
 */
class Folder extends Model
{
    use HasFactory;

    protected $guarded = [];

    // When a folder is updated, update the updated_at timestamp of the parent folder
    protected $touches = ['parentFolder',];

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
