<?php

namespace App\Http\Controllers\v1;

use App\Enums\Privileges;
use App\Http\Controllers\Controller;
use App\Models\Privilege;
use App\Models\User;
use App\Traits\MorphTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class PrivilegeApiController extends Controller
{
    use MorphTrait;
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(Privilege::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request['grantor_id'] = $request->user()->id;
        $request->validate([
            'action'        => 'required',
            'grantee_type'  => 'required',
            'grantee_id'    => 'required',
        ]);

        $request['type'] = Privileges::getType($request['action']);

        if ($request['type'] === 'file') {
            $request->validate([
                'target_id' => 'required',
                'target_type' => 'required'
            ]);
            if ($request->user()->cannot('edit', $this->getMorphedModel($request['target_id'], $request['target_type']))) {
                return response()->json(['message' => 'You do not have the right to share this item'], Response::HTTP_FORBIDDEN);
            }
        }
        else {
            if (Gate::denies('isAdmin', User::class)) {
                return response()->json(['message' => 'You do not have the right to give privileges'], Response::HTTP_FORBIDDEN);
            }
        }

        $privilege = Privilege::query()->create($request->all());
        return response()->json($privilege, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Privilege $privilege
     * @return JsonResponse
     */
    public function show(Privilege $privilege): JsonResponse
    {
        return response()->json($privilege);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Privilege $privilege
     * @return JsonResponse
     */
    public function update(Request $request, Privilege $privilege): JsonResponse
    {
        $privilege = Privilege::query()->findOrFail($privilege->id);
        $privilege->update($request->all());
        return response()->json($privilege);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Privilege $privilege
     * @return JsonResponse
     */
    public function destroy(Privilege $privilege): JsonResponse
    {
        $privilege = Privilege::query()->findOrFail($privilege->id);
        $privilege->delete();
        return response()->json(null, 204);
    }


}
