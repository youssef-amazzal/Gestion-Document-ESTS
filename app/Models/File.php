<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\Storage;

/**
 * Class File
 * @mixin QueryBuilder
 * @mixin EloquentBuilder
 * @property Folder $parentFolder
 * @property Space $space
 * @property User $owner
 * @property File $originalFile
 *
 */
class File extends Model
{
    use HasFactory;
    protected $guarded = ['path'];
    protected $hidden = ['path'];

    // When a file is updated, update the updated_at timestamp of the parent folder
    protected $touches = ['parentFolder',];

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
