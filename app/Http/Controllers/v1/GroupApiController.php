<?php

namespace App\Http\Controllers\v1;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(Group::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $group = Group::query()->create($request->all());
        return response()->json($group, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Group $group
     * @return JsonResponse
     */
    public function show(Group $group): JsonResponse
    {
        $group = Group::query()->findOrFail($group->id);
        return response()->json($group);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Group $group
     * @return JsonResponse
     */
    public function update(Request $request, Group $group): JsonResponse
    {
        $group = Group::query()->findOrFail($group->id);
        $group->update($request->all());
        return response()->json($group);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Group $group
     * @return JsonResponse
     */
    public function destroy(Group $group): JsonResponse
    {
        $group = Group::query()->findOrFail($group->id);
        $group->delete();
        return response()->json(null, 204);
    }

    public function getOwnedGroups(Request $request): JsonResponse
    {
        $owner = $request->user();
        $groups = Group::query()
            ->where('user_id', $owner->id)
            ->withCount(['filePrivileges', 'folderPrivileges', 'spacePrivileges'])
            ->with('users');

        return response()->json($groups->get());
    }

    public function getPotentialMembers(Request $request, Group $group): JsonResponse
    {
        $owner = $request->user();

        if ($owner->role === Roles::PROFESSOR) {
            $users = User::query()
                ->select(['users.*', DB::raw("CASE when users.id IN (SELECT user_id FROM group_user WHERE group_id = $group->id) THEN 1 ELSE 0 END AS is_member")])
                ->where('id', '!=', $owner->id)
                ->whereIn('id', $owner->students()->pluck('id'))
                ->get();
        } else {
            $users = User::query()
                ->select(['users.*', DB::raw("CASE when users.id IN (SELECT user_id FROM group_user WHERE group_id = $group->id) THEN 1 ELSE 0 END AS is_member")])
                ->where('id', '!=', $owner->id)
                ->get();
        }

        return response()->json($users);
    }

    public function toggleMembers(Request $request, Group $group): JsonResponse
    {
        $request->validate([
            'members' => 'required|array',
            'members.*' => 'required|integer|exists:users,id'
        ]);

        $owner = $request->user();
        if ($owner->id !== $group->user_id) {
            return response()->json(null, 403);
        }

        $membersIds = $request->get('members');
        $members = User::query()->whereIn('id', $membersIds)->get();
        $newMembers = $members->diff($group->users);
        $oldMembers = $members->intersect($group->users);

        $group->users()->attach($newMembers);
        $group->users()->detach($oldMembers);

        return response()->json($group->users);
    }

    public function rename(Request $request, Group $group): JsonResponse
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        $owner = $request->user();
        if ($owner->id !== $group->user_id) {
            return response()->json(null, 403);
        }

        $group->name = $request->get('name');
        $group->save();

        return response()->json($group);
    }
}
