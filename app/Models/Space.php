<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Space extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_permanent',
        'owner_id',
    ];

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function privileges() {
        return $this->morphMany(Privilege::class, 'target');
    }
    public function operations() {
        return $this->morphMany(Operation::class, 'trackable');
    }

    public function files() {
        return $this->hasMany(File::class, 'space_id');
    }

    public function folders() {
        return $this->hasMany(Folder::class, 'space_id');
    }
}
