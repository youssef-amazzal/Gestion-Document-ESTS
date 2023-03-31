<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


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
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required'
        ]);

        // Todo : we should consider encrypting the file before storing it

        $file = $request->file('file');

        $fileUpload = new File;
        $fileUpload->name = $file->getClientOriginalName();
        $fileUpload->description = $request->input('description');
        $fileUpload->path = $file->storeAs('uploads', $file->getClientOriginalName());
        $fileUpload->mime_type = $file->getClientMimeType();
        $fileUpload->size = $file->getSize();
        $fileUpload->owner_id = $request->user()->id;
        $fileUpload->save();

        return response()->json($fileUpload, 201);
    }
    public function download(File $file)
    {
        $file = File::query()->findOrFail($file->id);

        $filePath = storage_path('app\\' . $file->path);

        return response()->download($filePath, $file->name, [
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
            return response()->json(['message' => 'You do not own this file. [controller]'], 403);
        }
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
        Storage::disk('local')->delete($file->path);
        return response()->json(null, 204);
    }


}
