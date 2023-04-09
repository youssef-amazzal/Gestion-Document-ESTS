<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Roles;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property Roles $role
 * @property string $email
 * @property string $password
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $avatar
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Group> $ownedGroups
 * @property-read int|null $created_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $files
 * @property-read int|null $files_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Filiere> $filieres
 * @property-read int|null $filieres_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Folder> $folders
 * @property-read int|null $folders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Group> $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Operation> $operations
 * @property-read int|null $operations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Privilege> $privileges
 * @property-read int|null $privileges_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Space> $spaces
 * @property-read int|null $spaces_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected static function booted()
    {
        static::created(function (User $user) {
            $user->spaces()->create(['name' => 'Espace personnel', 'is_permanent' => true]);

            if ($user->role === Roles::PROFESSOR) {
                $filieres = $user->filieres;
                $filieres->each(function (Filiere $filiere) use ($user) {
                    $group = $user->ownedGroups()->create(['name' => $filiere->name, 'user_id' => $user->id]);
                    $students = $filiere->students()->get();
                    $group->users()->attach($students);
                });
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'email_verified_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => Roles::class,
    ];

    public function files() {
        return $this->hasMany(File::class, 'owner_id');
    }

    public function spaces() {
        return $this->hasMany(Space::class, 'owner_id');
    }

    public function tags() {
        return $this->hasMany(Tag::class);
    }

    public function folders() {
        return $this->hasMany(Folder::class, 'owner_id');
    }

    public function operations() {
        return $this->hasMany(Operation::class);
    }

    public function groups() {
        return $this->belongsToMany(Group::class);
    }

    public function privileges() {
        return $this->morphMany(Privilege::class, 'grantee');
    }

    public function ownedGroups() {
        return $this->hasMany(Group::class);
    }

    public function filieres(): BelongsToMany
    {
        return $this->belongsToMany(Filiere::class)->withPivot('year');
    }

    public function students($promotions = [])
    {
        if ($this->role === Roles::PROFESSOR) {
            $filieres = $this->filieres->pluck('id');
            $promotions = empty($promotions) ? $this->filieres->pluck('pivot.year') : $promotions;

            return User::query()
                ->select(['users.*', 'filieres.name as filiere_name', 'filiere_user.year as promotion', 'filieres.id as filiere_id'])
                ->join('filiere_user', 'users.id', '=', 'filiere_user.user_id')
                ->join('filieres', 'filieres.id', 'filiere_user.filiere_id')
                ->where('users.role', '=', Roles::STUDENT)
                ->whereIn('filieres.id', $filieres )
                ->whereIn('filiere_user.year', $promotions); // looks like laravel ignores the whereIn when the array is null or empty
        }
        return User::query()->where('id', '=', -1);
    }

    public function professors($promotions = null) {
        if ($this->role === Roles::STUDENT) {
            $filieres = $this->filieres->pluck('id');
            $promotions = empty($promotions) ? $this->filieres->pluck('pivot.year') : $promotions;

            return User::query()
                ->select(['users.*', 'filieres.name as filiere_name', 'filiere_user.year as promotion', 'filieres.id as filiere_id'])
                ->join('filieres', 'filieres.id', 'filiere_user.filiere_id')
                ->where('users.role', '=', Roles::PROFESSOR)
                ->whereIn('filieres.id', $filieres )
                ->whereIn('filiere_user.year', $promotions);
        }
        return null;
    }

}
