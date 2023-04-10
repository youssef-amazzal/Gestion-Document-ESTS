<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use App\Models\Space;
use App\Traits\ShareTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FolderApiController extends Controller
{
    use ShareTrait;
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
            'original_id' => $request['original_id'] ?? null,
			'size' => 0,
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

        $folder->update([
            'name' => $request->name,
            'parent_folder_id' => $request->parent_folder_id,
            'space_id' => $request->space_id,
            'owner_id' => $request->owner_id,
            'is_shortcut' => $request->is_shortcut,
            'original_id' => $request->original_id,
            'size' => $request->size ?? '0',
        ]);

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

        $files = $folder->files()->with(['owner','tags'])->orderBy('created_at')->get();
        foreach ($files as $file) {
            $file->groups = $file->groups();
            $file->users = $file->users();
        }

        $folders = $folder->folders()->with(['owner','tags'])->orderBy('created_at')->get();
        foreach ($folders as $folder) {
            $folder->groups = $folder->groups();
            $folder->users = $folder->users();
        }

        $merged = $folders->merge($files);

        return response()->json($merged);
    }

    public function getSharedWithMe(Request $request): JsonResponse
    {
        /*
         * the folders that are considered being shared with the user are those on where he or his group
         * have the right to view the file (direct) or the right to view an ancestor folder o space
         */

        $user = $request->user();

        // user has direct privileges on those folders
        $direct_shared_folders = $this->getDirectSharedResources($user,Folder::class);

        // user has privileges on ancestor folders of those folders
        $indirect_shared_folders = $this->getIndirectSharedResources($user,Folder::class);

        $shared_folders = $direct_shared_folders
            ->merge($indirect_shared_folders)
            ->limit($request->limit)
            ->with('owner')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($shared_folders as $folder) {
            $folder->groups = $folder->groups();
            $folder->users = $folder->users();
        }

        return response()->json($shared_folders);
    }

    public function getPinned(Request $request): JsonResponse
    {
        $user = $request->user();
        $pinned_folders = $user->folders()->where('is_pinned', true)->with('owner')->get();
        foreach ($pinned_folders as $folder) {
            $folder->groups = $folder->groups();
            $folder->users = $folder->users();
        }
        return response()->json($pinned_folders);
    }

    public function share(Request $request, Folder $folder): JsonResponse
    {
        $user = $request->user();
        return response()->json($this->manageShareResource($user, $request, $folder));
    }

    public function getPotentialMembers(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json($this->getSharees($user, $request));
    }

    public function togglePin(Request $request, Folder $folder): JsonResponse
    {
        if ($request->user()->cannot('edit', $folder)) {
            return response()->json(['message' => 'You do not have the required privileges to edit this file.'], Response::HTTP_FORBIDDEN);
        }
        $folder->pinned = !$folder->pinned;
        $folder->save();
        return response()->json($folder);
    }

    public function createShortcut(Request $request, Folder $folder): JsonResponse
    {
        $request->validate([
            'parent_folder_id' => 'required|integer',
            'space_id' => 'required|integer',
        ]);

        $user = $request->user();
        if ($user->cannot('view', $folder)) {
            return response()->json(['message' => 'You do not have the required privileges to create a shortcut for this file.'], Response::HTTP_FORBIDDEN);
        }

        $shortcut = Folder::make([
            'name'              => $folder->name,
            'parent_folder_id'  => $request['parent_folder_id'],
            'space_id'          => $request['space_id'],
            'owner_id'          => $user->id,
            'size'              => $folder->size,
            'shortcut'          => true,
            'original_id'       => $folder->id,
        ]);

        $shortcut->save();

        return response()->json($shortcut, Response::HTTP_CREATED);
    }

    public function move(Request $request, Folder $folder): JsonResponse
    {
        $user = $request->user();
        if ($user->cannot('edit', $folder)) {
            return response()->json(['message' => 'You do not have the required privileges to move this file.'], Response::HTTP_FORBIDDEN);
        }

        $folder->parent_folder_id = $request['parent_folder_id'];
        $folder->space_id = $request['space_id'];
        $folder->save();

        return response()->json($folder);
    }

    public function rename(Request $request, Folder $folder): JsonResponse
    {
        $user = $request->user();
        if ($user->cannot('edit', $folder)) {
            return response()->json(['message' => 'You do not have the required privileges to rename this file.'], Response::HTTP_FORBIDDEN);
        }

        $folder->name = $request['name'];
        $folder->save();

        return response()->json($folder);
    }
}
