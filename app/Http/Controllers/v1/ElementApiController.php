<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Element;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ElementApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(Element::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $element = Element::query()->create($request->all());
        return response()->json($element, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Element $element
     * @return JsonResponse
     */
    public function show(Element $element): JsonResponse
    {
        $element = Element::query()->findOrFail($element->id);
        return response()->json($element);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Element $element
     * @return JsonResponse
     */
    public function update(Request $request, Element $element): JsonResponse
    {
        $element = Element::query()->findOrFail($element->id);
        $element->update($request->all());
        return response()->json($element);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Element $element
     * @return JsonResponse
     */
    public function destroy(Element $element): JsonResponse
    {
        $element = Element::query()->findOrFail($element->id);
        $element->delete();
        return response()->json(null, 204);
    }
}
