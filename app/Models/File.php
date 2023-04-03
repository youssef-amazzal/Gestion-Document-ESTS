<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

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
    protected $touches = [
        'parentFolder',
    ];

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
