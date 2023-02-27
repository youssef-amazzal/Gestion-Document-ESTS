<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Filiere;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FiliereApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(){
        return response()->json(Filiere::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $filiere = Filiere::create($request->all());
        return response()->json($filiere, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Filiere $filiere
     * @return JsonResponse
     */
    public function show(Filiere $filiere): JsonResponse
    {
        $filiere = Filiere::findOrFail($filiere->id);
        return response()->json($filiere);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Filiere $filiere
     * @return JsonResponse
     */
    public function update(Request $request, Filiere $filiere): JsonResponse
    {
        $filiere = Filiere::findOrFail($filiere->id);
        $filiere->update($request->all());
        return response()->json($filiere);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Filiere $filiere
     * @return JsonResponse
     */
    public function destroy(Filiere $filiere): JsonResponse
    {
        $filiere = Filiere::findOrFail($filiere->id);
        $filiere->delete();
        return response()->json(null, 204);
    }
}
