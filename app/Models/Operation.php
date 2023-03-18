<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Class File
 * @mixin QueryBuilder
 * @mixin EloquentBuilder
 */
class Operation extends Model
{
    use HasFactory;

    public function trackable(): MorphTo
    {
        return $this->morphTo('trackable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }




}
