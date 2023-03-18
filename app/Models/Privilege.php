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
 * @mixin QueryBuilder
 * @mixin EloquentBuilder
 *
 * @property int $id
 * @property string $action
 * @property string $type
 *
 */
class Privilege extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'type',
        'target_id',
        'grantee_type',
        'grantee_id',
        'grantor_id',
    ];

    protected $casts = [
        'action' => Privileges::class,
    ];

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class, 'target_id');
    }

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
