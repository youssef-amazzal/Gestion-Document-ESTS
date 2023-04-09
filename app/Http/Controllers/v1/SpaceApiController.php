<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Space;
use App\Traits\ShareTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SpaceApiController extends Controller
{
    use ShareTrait;
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(Space::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $space = Space::create([
            'name' => $request['name'],
            'owner_id' => $request->user()->id,
        ]);

        return response()->json($space, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Space $space
     * @return JsonResponse
     */
    public function show(Request $request, Space $space)
    {
        if ($request->user()->cannot('view', $space)) {
            return response()->json(['message' => 'You do not have the right to view this space.'], Response::HTTP_FORBIDDEN);
        }

        return response()->json($space);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Space $space
     * @return JsonResponse
     */
    public function update(Request $request, Space $space)
    {
        if ($request->user()->cannot('edit', $space)) {
            return response()->json(['message' => 'You do not have the right to edit this space.'], Response::HTTP_FORBIDDEN);
        }

        $request->validate([
            'name' => 'required',
        ]);

        $space->update([
            'name' => $request['name'],
        ]);

        return response()->json($space);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Space $space
     * @return JsonResponse
     */
    public function destroy(Request $request, Space $space)
    {
        if ($request->user()->cannot('edit', $space)) {
            return response()->json(['message' => 'You do not have the right to delete this space.'], Response::HTTP_FORBIDDEN);
        }

        $space->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function getPersonalSpaces(Request $request): JsonResponse
    {
        $spaces = $request->user()->spaces()
            ->with('owner')
            ->withCount(['files', 'folders'])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($spaces as $space) {
            $space->groups = $space->groups();
            $space->users = $space->users();
        }

        return response()->json($spaces);
    }

    public function getSharedWithMe(Request $request): JsonResponse
    {
        /*
         * the spaces that are considered being shared with the user are those on where he or his group
         * have the right to view the space
         */

        $user = $request->user();

        // user has direct privileges on those spaces
        $spaces = $this->getDirectSharedResources($user, Space::class)
            ->orderBy('shared_at', 'desc')
            ->with('owner')->get();

        foreach ($spaces as $space) {
            $space->groups = $space->groups();
            $space->users = $space->users();
        }

        return response()->json($spaces);
    }

    public function getContent(Request $request, Space $space): JsonResponse
    {
        if ($request->user()->cannot('view', $space)) {
            return response()->json(['message' => 'You do not have the right to view this space.'], Response::HTTP_FORBIDDEN);
        }

        $files = $space->files()->doesntHave('parentFolder')->get();
        $folders = $space->folders()->doesntHave('parentFolder')->get();

        $merged = $files->merge($folders);

        return response()->json($merged);
    }

    public function share(Request $request, Space $space): JsonResponse
    {
        $user = $request->user();
        return response()->json($this->manageShareResource($user, $request, $space));
    }

    public function getPotentialMembers(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json($this->getSharees($user, $request));
    }
}
