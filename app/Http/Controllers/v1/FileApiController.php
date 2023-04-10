<?php

namespace App\Http\Controllers\v1;

use App\Enums\Privileges;
use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Folder;
use App\Models\Group;
use App\Models\Space;
use App\Models\User;
use App\Traits\PathTrait;
use App\Traits\ShareTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;


class FileApiController extends Controller
{
    use PathTrait, ShareTrait;
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
    public function upload(Request $request): JsonResponse
    {
        $user = $request->user();

        // check if the user has the right to upload files
        if ($user->cannot('upload', User::class)) {
            return response()->json(['message' => 'You do not have the right to upload files.'], Response::HTTP_FORBIDDEN);
        }


        $request->validate([
            'file' => 'required',
            'space_id' => 'required|exists:spaces,id',
        ]);

        // check if the user has the right to upload the file either in the space or in the folder

        if ($request->filled('parent_folder_id')) {
            $parentFolder = Folder::query()->find($request['parent_folder_id']);
            $request['space_id'] = $parentFolder->space_id; //just in case the space_id is wrong

            if ($user->cannot('uploadInto', $parentFolder)) {
                return response()->json([
                    'message' => 'You do not have the right to upload files in this folder.',
                ], Response::HTTP_FORBIDDEN);
            }
        } else {
            $space = Space::query()->find($request['space_id']);

            if ($user->cannot('uploadInto', $space)) {
                return response()->json([
                    'message' => 'You do not have the right to upload files in this space.',
                    ], Response::HTTP_FORBIDDEN);
            }
        }


        $binaryFile = $request->file('file');

        $file = File::make([
            'name'              => pathinfo($binaryFile->getClientOriginalName(), PATHINFO_FILENAME), // get the filename without the extension
            'parent_folder_id'  => $request['parent_folder_id'],
            'space_id'          => $request['space_id'],
            'owner_id'          => $user->id,
            'mime_type'         => $binaryFile->getMimeType(),
            'size'              => $binaryFile->getSize(),
        ]);

        $file->path = $binaryFile->store($this->pathFromTarget($file));
        $file->save();

        return response()->json($file, Response::HTTP_CREATED);
    }
    public function download(Request $request, File $file)
    {
        if ($request->user()->cannot('view', $file)) {
            return response()->json(['message' => 'You do not have the required privileges to download this file.'], Response::HTTP_FORBIDDEN);
        }

        $filePath = storage_path('app\\' . $file->path);

        return response()->download($filePath, $file->name, [
            'file-name' => $file->name,
            'Content-Type' => $file->mime_type,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param File $file
     * @return JsonResponse
     */
    public function show(Request $request, File $file): JsonResponse
    {
        if ($request->user()->cannot('view', $file)) {
            return response()->json(['message' => 'You do not have the required privileges to view this file.'], Response::HTTP_FORBIDDEN);
        }

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
        if ($request->user()->cannot('edit', $file)) {
            return response()->json(['message' => 'You do not have the required privileges to edit this file.'], Response::HTTP_FORBIDDEN);
        }

        $file->update([
            'name' => $request->name,
            'parent_folder_id' => $request->parent_folder_id,
            'space_id' => $request->space_id,
            'owner_id' => $request->owner_id,
            'is_shortcut' => $request->is_shortcut,
            'original_id' => $request->original_id,
            'size' => $request->size ?? '0',
            'mime_type' => $request->mime_type,
            'path' => $request->path
        ]);
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
        if (Gate::denies('edit', $file)) {
            return response()->json(['message' => 'You do not have the required privileges to delete this file.'], Response::HTTP_FORBIDDEN);
        }

        $file->delete();
        Storage::disk('local')->delete($file->path);
        return response()->json(null, 204);
    }

    public function getSharedWithMe(Request $request): JsonResponse
    {
        /*
         * the files that are considered being shared with the user are those on where he or his group
         * have the right to view the file (direct) or the right to view an ancestor folder o space
         */

        $user = $request->user();

        // user has direct privileges on those files
        $direct_shared_files = $this->getDirectSharedResources($user,File::class);

        // user has privileges on ancestor folders of those files
        $indirect_shared_files = $this->getIndirectSharedResources($user,File::class);

        $shared_files = $direct_shared_files
            ->union($indirect_shared_files)
            ->limit($request->limit)
            ->with('owner')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($shared_files as $file) {
            $file->groups = $file->groups();
            $file->users = $file->users();
        }

        return response()->json($shared_files);
    }

    public function getPinned(Request $request): JsonResponse
    {
        $user = $request->user();
        $pinned_files = $user->files()->where('is_pinned', true)->with('owner')->get();
        foreach ($pinned_files as $file) {
            $file->groups = $file->groups();
            $file->users = $file->users();
        }
        return response()->json($pinned_files);
    }

    public function share(Request $request, File $file): JsonResponse
    {
        $user = $request->user();
        return response()->json($this->manageShareResource($user, $request, $file));
    }

    public function getPotentialMembers(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json($this->getSharees($user, $request));
    }

    public function togglePin(Request $request, File $file): JsonResponse
    {
        if ($request->user()->cannot('edit', $file)) {
            return response()->json(['message' => 'You do not have the required privileges to edit this file.'], Response::HTTP_FORBIDDEN);
        }
        $file->pinned = !$file->pinned;
        $file->save();
        return response()->json($file);
    }

    public function createShortcut(Request $request, File $file): JsonResponse
    {
        $request->validate([
            'parent_folder_id' => 'required|integer',
            'space_id' => 'required|integer',
        ]);

        $user = $request->user();
        if ($user->cannot('view', $file)) {
            return response()->json(['message' => 'You do not have the required privileges to create a shortcut for this file.'], Response::HTTP_FORBIDDEN);
        }

        $shortcut = File::make([
            'name'              => $file->name,
            'parent_folder_id'  => $request['parent_folder_id'],
            'space_id'          => $request['space_id'],
            'owner_id'          => $user->id,
            'mime_type'         => $file->mime_type,
            'size'              => $file->size,
            'path'              => $file->path,
            'shortcut'          => true,
            'original_id'       => $file->id,
        ]);

        $shortcut->save();

        return response()->json($shortcut, Response::HTTP_CREATED);
    }

    public function move(Request $request, File $file): JsonResponse
    {
        $user = $request->user();
        if ($user->cannot('edit', $file)) {
            return response()->json(['message' => 'You do not have the required privileges to move this file.'], Response::HTTP_FORBIDDEN);
        }

        $file->parent_folder_id = $request['parent_folder_id'];
        $file->space_id = $request['space_id'];
        $file->save();

        return response()->json($file);
    }

    public function rename(Request $request, File $file): JsonResponse
    {
        $user = $request->user();
        if ($user->cannot('edit', $file)) {
            return response()->json(['message' => 'You do not have the required privileges to rename this file.'], Response::HTTP_FORBIDDEN);
        }

        $file->name = $request['name'];
        $file->save();

        return response()->json($file);
    }
}
