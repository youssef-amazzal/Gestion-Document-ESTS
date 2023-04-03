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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;


class FileApiController extends Controller
{
    use PathTrait;
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
            'parent_folder_id' => 'nullable|exists:folders,id',
            'space_id' => 'required|exists:spaces,id',
        ]);

        // check if the user has the right to upload the file either in the space or in the folder
        $parentFolder = Folder::query()->find($request['parent_folder_id']);
        if ($parentFolder) {

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
            'mime_type' => $file->mime_type,
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
        if (Gate::denies('delete', $file)) {
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
        $groups = $user->groups->pluck('id');

        // user has direct privileges on those files
        $direct_shared_files =
            File::query()->select(['files.*', 'privileges.updated_at as shared_at'])
            ->join('privileges', function ($join) use ($groups, $user) {
                $join->on('privileges.target_id', '=', 'files.id')

                    ->where('privileges.target_type', '=', File::class)

                    ->where(function ($query) use ($groups, $user) {

                        $query->where(function ($query) use ($user) {
                                $query->where('privileges.grantee_id', '=', $user->id)
                                    ->where('privileges.grantee_type', '=', User::class);
                        })
                              ->orWhere(function ($query) use ($groups) {
                                $query->whereIn('privileges.grantee_id', $groups)
                                    ->where('privileges.grantee_type', '=', Group::class);
                        });
                    });
            })->orderBy('shared_at', 'desc');

        // user has privileges on ancestor folders of those files
        $indirect_shared_files =
            File::query()->select(['files.*', 'privileges.updated_at as shared_at'])
            ->join('containables', function ($join) {
                $join->on('containables.containable_id', '=', 'files.id')
                    ->where('containables.containable_type', '=', File::class);
            })
            ->join('folders', 'folders.id', '=', 'containables.folder_id')
            ->join('privileges', function ($join) use ($groups, $user) {
                $join->on('privileges.target_id', '=', 'folders.id')
                    ->where('privileges.target_type', '=', Folder::class)
                    ->where(function ($query) use ($groups, $user) {

                        $query->where(function ($query) use ($user) {
                                $query->where('privileges.grantee_id', '=', $user->id)
                                    ->where('privileges.grantee_type', '=', User::class);
                        })
                              ->orWhere(function ($query) use ($groups) {
                                $query->whereIn('privileges.grantee_id', $groups)
                                    ->where('privileges.grantee_type', '=', Group::class);
                        });
                    });
            });

        // user has privileges on the parent space of those files
        $indirect_shared_files = $indirect_shared_files->union(
            File::query()->select(['files.*', 'privileges.updated_at as shared_at'])
            ->join('spaces', 'spaces.id', '=', 'files.space_id')
            ->join('privileges', function ($join) use ($groups, $user) {
                $join->on('privileges.target_id', '=', 'spaces.id')
                    ->where('privileges.target_type', '=', Space::class)
                    ->where(function ($query) use ($groups, $user) {

                        $query->where(function ($query) use ($user) {
                                $query->where('privileges.grantee_id', '=', $user->id)
                                    ->where('privileges.grantee_type', '=', User::class);
                        })
                              ->orWhere(function ($query) use ($groups) {
                                $query->whereIn('privileges.grantee_id', $groups)
                                    ->where('privileges.grantee_type', '=', Group::class);
                        });
                    });
            }));

        return response()
            ->json($direct_shared_files
            ->union($indirect_shared_files)
            ->orderBy('shared_at', 'desc')
            ->limit($request->limit)
            ->with('owner')->get());
    }

}
