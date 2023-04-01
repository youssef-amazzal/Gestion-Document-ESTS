<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Space;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SpaceApiController extends Controller
{
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
        if ($request->user()->cannot('delete', $space)) {
            return response()->json(['message' => 'You do not have the right to delete this space.'], Response::HTTP_FORBIDDEN);
        }

        $space->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
