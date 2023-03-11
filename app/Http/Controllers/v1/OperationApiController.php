<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Operation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OperationApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(Operation::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $operation = Operation::query()->create($request->all());
        return response()->json($operation, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Operation $operation
     * @return JsonResponse
     */
    public function show(Operation $operation): JsonResponse
    {
        $operation = Operation::query()->findOrFail($operation->id);
        return response()->json($operation);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Operation $operation
     * @return JsonResponse
     */
    public function update(Request $request, Operation $operation): JsonResponse
    {
        $operation = Operation::query()->findOrFail($operation->id);
        $operation->update($request->all());
        return response()->json($operation);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Operation $operation
     * @return JsonResponse
     */
    public function destroy(Operation $operation): JsonResponse
    {
        $operation = Operation::query()->findOrFail($operation->id);
        $operation->delete();
        return response()->json(null, 204);
    }
}
