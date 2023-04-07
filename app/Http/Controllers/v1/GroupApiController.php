<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
}
