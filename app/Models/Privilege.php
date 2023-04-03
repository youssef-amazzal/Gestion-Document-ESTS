<?php

namespace App\Models;

use App\Enums\Privileges;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Class File
 *
 * @mixin QueryBuilder
 * @mixin EloquentBuilder
 * @property int $id
 * @property string $action
 * @property string $type
 * @property int|null $grantor_id
 * @property int|null $target_id
 * @property string|null $target_type
 * @property int $grantee_id
 * @property string $grantee_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $grantee
 * @property-read \App\Models\User|null $grantor
 * @property-read Model|\Eloquent $target
 * @method static \Database\Factories\PrivilegeFactory factory($count = null, $state = [])
 * @method static EloquentBuilder|Privilege newModelQuery()
 * @method static EloquentBuilder|Privilege newQuery()
 * @method static EloquentBuilder|Privilege query()
 * @method static EloquentBuilder|Privilege whereAction($value)
 * @method static EloquentBuilder|Privilege whereCreatedAt($value)
 * @method static EloquentBuilder|Privilege whereGranteeId($value)
 * @method static EloquentBuilder|Privilege whereGranteeType($value)
 * @method static EloquentBuilder|Privilege whereGrantorId($value)
 * @method static EloquentBuilder|Privilege whereId($value)
 * @method static EloquentBuilder|Privilege whereTargetId($value)
 * @method static EloquentBuilder|Privilege whereTargetType($value)
 * @method static EloquentBuilder|Privilege whereType($value)
 * @method static EloquentBuilder|Privilege whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Privilege extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'action' => Privileges::class,
    ];

    public function grantee(): MorphTo
    {
        return $this->morphTo('grantee');
    }

    public function grantor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'grantor_id');
    }

    public function target(): MorphTo
    {
        return $this->morphTo('target');
    }

}
