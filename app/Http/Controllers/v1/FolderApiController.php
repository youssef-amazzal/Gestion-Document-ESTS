<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use App\Models\Space;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $request->validate([
            'name' => 'required',
            'parent_folder_id' => 'nullable|exists:folders,id',
            'space_id' => 'required|exists:spaces,id',
        ]);

        $user = $request->user();

        if (isset($request['parent_folder_id'])) {
            $parentFolder = Folder::query()->find($request['parent_folder_id']);

            if ($user->cannot('uploadInto', $parentFolder)) {
                return response()->json(['message' => 'You do not have the right to create folders in this folder.'], Response::HTTP_FORBIDDEN);
            }
        } else {
            $space = Space::query()->find($request['space_id']);

            if ($user->cannot('uploadInto', $space)) {
                return response()->json(['message' => 'You do not have the right to create folders in this space.'], Response::HTTP_FORBIDDEN);
            }
        }

        $folder = Folder::create([
            'name' => $request['name'],
            'parent_folder_id' => $request['parent_folder_id'],
            'space_id' => $request['space_id'],
            'owner_id' => $user->id,
            'is_shortcut' => $request['is_shortcut'] ?? false,
            'shortcut_target_id' => $request['shortcut_target_id'] ?? null,
        ]);

        return response()->json($folder, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Folder $folder
     * @return JsonResponse
     */
    public function show(Request $request, Folder $folder): JsonResponse
    {
        if ($request->user()->cannot('view', $folder)) {
            return response()->json(['message' => 'You do not have the required privileges to access to this folder.'], Response::HTTP_FORBIDDEN);
        }
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
        if ($request->user()->cannot('edit', $folder)) {
            return response()->json(['message' => 'You do not have the required privileges to edit this folder.'], Response::HTTP_FORBIDDEN);
        }

        $folder->update($request->all());
        return response()->json($folder);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Folder $folder
     * @return JsonResponse
     */
    public function destroy(Request $request, Folder $folder): JsonResponse
    {
        if ($request->user()->cannot('edit', $folder)) {
            return response()->json(['message' => 'You do not have the required privileges to delete this folder.'], Response::HTTP_FORBIDDEN);
        }
        $folder->delete();
        return response()->json(null, 204);
    }

    public function getContent(Request $request, Folder $folder): JsonResponse
    {
        if ($request->user()->cannot('view', $folder)) {
            return response()->json(['message' => 'You do not have the right to view this folder.'], Response::HTTP_FORBIDDEN);
        }

        $files = $folder->files()->get();
        $folders = $folder->folders()->get();

        $merged = $files->merge($folders);

        return response()->json($merged);
    }
}
