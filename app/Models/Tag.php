<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $files
 * @property-read int|null $files_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Folder> $folders
 * @property-read int|null $folders_count
 * @method static \Database\Factories\TagFactory factory($count = null, $state = [])
 * @method static EloquentBuilder|Tag newModelQuery()
 * @method static EloquentBuilder|Tag newQuery()
 * @method static EloquentBuilder|Tag query()
 * @method static EloquentBuilder|Tag whereCreatedAt($value)
 * @method static EloquentBuilder|Tag whereId($value)
 * @method static EloquentBuilder|Tag whereName($value)
 * @method static EloquentBuilder|Tag whereUpdatedAt($value)
 * @method static EloquentBuilder|Tag whereUserId($value)
 * @mixin \Eloquent
 */
class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function files() {
        return $this->morphedByMany(File::class, 'taggable');
    }

    public function folders() {
        return $this->morphedByMany(Folder::class, 'taggable');
    }
}
