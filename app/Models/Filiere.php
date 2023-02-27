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
class Filiere extends Model
{
    use HasFactory;

    protected $fillable = ['name, abbreviation', 'promotion'];

    public function elements() {
        return $this->belongsToMany(Element::class);
    }
}
