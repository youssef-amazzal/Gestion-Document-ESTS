<?php

namespace App\Models;

use App\Enums\Roles;
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
 * @property string $abbreviation
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $professors
 * @property-read int|null $professors_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $students
 * @property-read int|null $students_count
 * @method static \Database\Factories\FiliereFactory factory($count = null, $state = [])
 * @method static EloquentBuilder|Filiere newModelQuery()
 * @method static EloquentBuilder|Filiere newQuery()
 * @method static EloquentBuilder|Filiere query()
 * @method static EloquentBuilder|Filiere whereAbbreviation($value)
 * @method static EloquentBuilder|Filiere whereCreatedAt($value)
 * @method static EloquentBuilder|Filiere whereId($value)
 * @method static EloquentBuilder|Filiere whereName($value)
 * @method static EloquentBuilder|Filiere whereType($value)
 * @method static EloquentBuilder|Filiere whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Filiere extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'abbreviation', 'promotion'];

    public function students() {
        return $this->belongsToMany(User::class)->where('role', Roles::STUDENT);
    }

    public function professors() {
        return $this->belongsToMany(User::class)->where('role', Roles::PROFESSOR);
    }
}
