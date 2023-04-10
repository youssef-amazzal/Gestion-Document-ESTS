<?php
namespace App\Traits;

use App\Enums\Privileges;
use App\Enums\Roles;
use App\Models\File;
use App\Models\Folder;
use App\Models\Group;
use App\Models\Privilege;
use App\Models\Space;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Validator;

trait ShareTrait {

    public function groups() {
        $groups = Group::query()->whereHas('privileges', function ($query) {
            $query->where('target_id', $this->id);
            $query->where('target_type', static::class);
        })->get();

        foreach ($groups as $group) {
            $group->privilege = $group->privileges
                ->where('target_id', $this->id)
                ->where('target_type', static::class);
        }

        return $groups;
    }

    public function users() {
        $users = User::query()->whereHas('privileges', function ($query) {
            $query->where('target_id', $this->id);
            $query->where('target_type', static::class);
        })->get();

        foreach ($users as $user) {
            $user->privilege = $user->privileges
                ->where('target_id', $this->id)
                ->where('target_type', static::class);
        }

        return $users;
    }

    public function manageShareResource(User $user, Request $request, Space|Folder|File $resource) {
        $response= [];

        $validator = Validator::make($request->all(), [
            'data.*.grantee_id' => 'required',
            'data.*.grantee_type' => 'required',
            'data.*.priv_type' => "required|in:" . implode(',', array_column(Privileges::filePrivileges(), 'value')),
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        foreach ($request->all() as $shareAction) {
            if ($shareAction['action'] === 'share') {
                $response[] = $this->shareResource($user, $shareAction, $resource);
            } else if ($shareAction['action'] === 'unshare') {
                $response[] = $this->unshareResource($user, $shareAction, $resource);
            } else {
                $response[] = response()->json(['message' => 'Invalid action.'], Response::HTTP_BAD_REQUEST);
            }
        }

        return $response;
    }
    private function shareResource(User $user, $shareAction, Space|Folder|File $resource)
    {
        if ($user->cannot('share', $resource)) {
            return response()->json(['message' => 'You do not have the right to share this resource.'], Response::HTTP_FORBIDDEN);
        }

        if ($shareAction['grantee_type'] === 'user') {
            $grantee = User::find($shareAction['grantee_id']);
        } else if ($shareAction['grantee_type'] === 'group') {
            $grantee = Group::find($shareAction['grantee_id']);
        } else {
            return response()->json(['message' => 'Invalid grantee type.'], Response::HTTP_BAD_REQUEST);
        }

        $privilegeType = $shareAction['priv_type'];
        $privilegeType = Privileges::from($privilegeType);

        $privilege = Privilege::query()
            ->where('target_id', $resource->id)
            ->where('target_type', $resource::class)
            ->where('grantee_id', $grantee->id)
            ->where('grantee_type', $grantee::class)
            ->first();

        if ($privilege) {
            $privilege->action = $privilegeType;
            $privilege->save();
            return $privilege;
        }

        return $resource->privileges()->create([
            'grantor_id' => $user->id,
            'grantee_id' => $grantee->id,
            'grantee_type' => get_class($grantee),
            'action' => $privilegeType,
        ]);
    }

    private function unshareResource(User $user, $shareAction, Space|Folder|File $resource)
    {
        if ($user->cannot('share', $resource)) {
            return response()->json(['message' => 'You do not have the right to share this resource.'], Response::HTTP_FORBIDDEN);
        }

        if ($shareAction['grantee_type'] === 'user') {
            $grantee = User::find($shareAction['grantee_id']);
        } else if ($shareAction['grantee_type'] === 'group') {
            $grantee = Group::find($shareAction['grantee_id']);
        } else {
            return response()->json(['message' => 'Invalid grantee type.'], Response::HTTP_BAD_REQUEST);
        }

        $privilege = Privilege::query()
            ->where('target_id', $resource->id)
            ->where('target_type', $resource::class)
            ->where('grantee_id', $grantee->id)
            ->where('grantee_type', $grantee::class)
            ->first();

        if ($privilege) {
            $privilege->delete();
            return response()->json(['message' => 'Resource unshared successfully.'], Response::HTTP_OK);
        }

        return response()->json(['message' => 'Resource was not shared with this grantee.'], Response::HTTP_BAD_REQUEST);
    }

    public function getSharees(User $user, Request $request) {

        $groups = $user->ownedGroups;
        $users = $user->role === Roles::PROFESSOR ? $user->students()->get() : User::all();

        if ($request->filled('target_type') && $request->filled('target_id')) {

            if ($request['target_type'] === 'space') {
                $resource = Space::find($request['target_id']);
            } else if ($request['target_type'] === 'folder') {
                $resource = Folder::find($request['target_id'])->space;
            } else if ($request['target_type'] === 'file') {
                $resource = File::find($request['target_id'])->space;
            } else {
                return response()->json(['message' => 'Invalid target type.'], Response::HTTP_BAD_REQUEST);
            }


            $privileges = Privilege::query()
                ->where('target_id', $resource->id)
                ->where('target_type', $resource::class)
                ->where(function ($query) use ($groups, $users) {
                    $query->where(function ($query) use ($users) {
                        $query->whereIn('grantee_id', $users->pluck('id'))
                            ->where('grantee_type', '=', User::class);
                    })
                        ->orWhere(function ($query) use ($groups) {
                            $query->whereIn('grantee_id', $groups->pluck('id'))
                                ->where('grantee_type', '=', Group::class);
                        });
                })
                ->get();

            $users->each(function ($user) use ($privileges) {
                $user->privilege = $privileges->filter(function ($privilege) use ($user) {
                    return $privilege->grantee_id === $user->id && $privilege->grantee_type === User::class;
                })->first();
            });

            $groups->each(function ($group) use ($privileges) {
                $group->privilege = $privileges->filter(function ($privilege) use ($group) {
                    return $privilege->grantee_id === $group->id && $privilege->grantee_type === Group::class;
                })->first();
            });
        }

        return [
            'users' => $users,
            'groups' => $groups,
        ];
    }

    public function getDirectSharedResources(User $user, string $resourceClass) {

        $tableName = (new $resourceClass)->getTable();
        $groups = $user->groups->pluck('id');

        return $resourceClass::query()->select(["$tableName.*", 'privileges.updated_at as shared_at'])
            ->join('privileges', function ($join) use ($groups, $user, $tableName) {
                $join->on('privileges.target_id', '=', "$tableName.id")

                    ->where('privileges.target_type', '=', Space::class)

                    ->where(function ($query) use ($groups, $user) {

                        $query->where(function ($query) use ($user) {
                            $query->where('privileges.grantee_id', '=', $user->id)
                                ->where('privileges.grantee_type', '=', User::class);
                        })
                            ->orWhere(function ($query) use ($groups) {
                                $query->whereIn('privileges.grantee_id', $groups)
                                    ->where('privileges.grantee_type', '=', Group::class);
                            });
                    });

            });
    }

    public function getIndirectSharedResources(User $user, string $resourceClass) {

        $tableName = (new $resourceClass)->getTable();
        $groups = $user->groups->pluck('id');

        $resources = $resourceClass::query()->select(["$tableName.*", 'privileges.updated_at as shared_at'])
            ->join('containables', function ($join) use ($resourceClass, $tableName) {
                $join->on('containables.containable_id', '=', "$tableName.id")
                    ->where('containables.containable_type', '=', $resourceClass);
            })
            ->join("folders", "folders.id", '=', 'containables.folder_id')
            ->join('privileges', function ($join) use ($groups, $user) {
                $join->on('privileges.target_id', '=', "folders.id")
                    ->where('privileges.target_type', '=', Folder::class)
                    ->where(function ($query) use ($groups, $user) {

                        $query->where(function ($query) use ($user) {
                            $query->where('privileges.grantee_id', '=', $user->id)
                                ->where('privileges.grantee_type', '=', User::class);
                        })
                            ->orWhere(function ($query) use ($groups) {
                                $query->whereIn('privileges.grantee_id', $groups)
                                    ->where('privileges.grantee_type', '=', Group::class);
                            });
                    });
            });

        return $resources->union(
            $resourceClass::query()->select(["$tableName.*", 'privileges.updated_at as shared_at'])
                ->join('spaces', 'spaces.id', '=', "$tableName.space_id")
                ->join('privileges', function ($join) use ($groups, $user) {
                    $join->on('privileges.target_id', '=', 'spaces.id')
                        ->where('privileges.target_type', '=', Space::class)
                        ->where(function ($query) use ($groups, $user) {

                            $query->where(function ($query) use ($user) {
                                $query->where('privileges.grantee_id', '=', $user->id)
                                    ->where('privileges.grantee_type', '=', User::class);
                            })
                                ->orWhere(function ($query) use ($groups) {
                                    $query->whereIn('privileges.grantee_id', $groups)
                                        ->where('privileges.grantee_type', '=', Group::class);
                                });
                        });
                }));
    }
}
