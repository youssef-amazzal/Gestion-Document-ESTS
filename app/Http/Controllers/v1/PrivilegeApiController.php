<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Privilege;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrivilegeApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(){
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
        $privilege = Privilege::create($request->all());
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
        $privilege = Privilege::findOrFail($privilege->id);
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
        $privilege = Privilege::findOrFail($privilege->id);
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
        $privilege = Privilege::findOrFail($privilege->id);
        $privilege->delete();
        return response()->json(null, 204);
    }
}