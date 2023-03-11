<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FolderApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(Folder::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $folder = Folder::query()->create($request->all());
        return response()->json($folder, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Folder $folder
     * @return JsonResponse
     */
    public function show(Folder $folder): JsonResponse
    {
        $folder = Folder::query()->findOrFail($folder->id);
        return response()->json($folder);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Folder $folder
     * @return JsonResponse
     */
    public function update(Request $request, Folder $folder): JsonResponse
    {
        $folder = Folder::query()->findOrFail($folder->id);
        $folder->update($request->all());
        return response()->json($folder);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Folder $folder
     * @return JsonResponse
     */
    public function destroy(Folder $folder): JsonResponse
    {
        $folder = Folder::query()->findOrFail($folder->id);
        $folder->delete();
        return response()->json(null, 204);
    }
}
