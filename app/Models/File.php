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
 */
class File extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'size',
        'mime_type',
        'path',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'owner_id');
    }

}
