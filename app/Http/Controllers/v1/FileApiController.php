<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PHPUnit\Util\Filesystem;

class FileApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(File::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $file = File::query()->create($request->all());
        return response()->json($file, 201);
    }

    public function upload(Request $request): JsonResponse
    {
        $file = Filesystem::putFile('public', $request->file('file'));
    }

    /**
     * Display the specified resource.
     *
     * @param File $file
     * @return JsonResponse
     */
    public function show(File $file): JsonResponse
    {
        $file = File::query()->findOrFail($file->id);
        return response()->json($file);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param File $file
     * @return JsonResponse
     */
    public function update(Request $request, File $file): JsonResponse
    {
        $file = File::query()->findOrFail($file->id);
        $file->update($request->all());
        return response()->json($file);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param File $file
     * @return JsonResponse
     */
    public function destroy(File $file): JsonResponse
    {
        $file = File::query()->findOrFail($file->id);
        $file->delete();
        return response()->json(null, 204);
    }
}
