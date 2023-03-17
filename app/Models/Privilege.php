<?php

namespace App\Models;

use App\Enums\Privileges;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Class File
 * @mixin QueryBuilder
 * @mixin EloquentBuilder
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $granted_to
 * @property string $granted_by
 * @property string $granted_on
 *
 */
class Privilege extends Model
{
    use HasFactory;

    protected $casts = [
        'name' => Privileges::class,
    ];

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class, 'granted_on');
    }

    public function grantee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_to');
    }

    public function grantor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

}
