<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

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
    protected $touches = [
        'parentFolder',
    ];

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function parentFolder() {
        return $this->belongsTo(Folder::class, 'parent_folder_id');
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
