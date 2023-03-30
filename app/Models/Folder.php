<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Class File
 * @mixin QueryBuilder
 * @mixin EloquentBuilder
 */
class Folder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'path',
    ];

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function parentFolder() {
        return $this->belongsTo(Folder::class, 'folder_id');
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
        return $this->hasMany(File::class, 'folder_id');
    }

    public function folders() {
        return $this->hasMany(Folder::class, 'folder_id');
    }

    public function tags() {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function operations() {
        return $this->morphMany(Operation::class, 'trackable');
    }
}
