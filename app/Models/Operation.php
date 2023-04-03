<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Class File
 *
 * @mixin QueryBuilder
 * @mixin EloquentBuilder
 * @property int $id
 * @property int $user_id
 * @property int $trackable_id
 * @property string $trackable_type
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $trackable
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\OperationFactory factory($count = null, $state = [])
 * @method static EloquentBuilder|Operation newModelQuery()
 * @method static EloquentBuilder|Operation newQuery()
 * @method static EloquentBuilder|Operation query()
 * @method static EloquentBuilder|Operation whereCreatedAt($value)
 * @method static EloquentBuilder|Operation whereId($value)
 * @method static EloquentBuilder|Operation whereTrackableId($value)
 * @method static EloquentBuilder|Operation whereTrackableType($value)
 * @method static EloquentBuilder|Operation whereType($value)
 * @method static EloquentBuilder|Operation whereUpdatedAt($value)
 * @method static EloquentBuilder|Operation whereUserId($value)
 * @mixin \Eloquent
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
