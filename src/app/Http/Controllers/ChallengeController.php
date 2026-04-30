<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChallengeResource;
use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChallengeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $challenges = Challenge::all();
        return response()->json(['vyzvy' => $challenges], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $challenge = Challenge::with(['attachments', 'program_a_categories'])->findOrFail($id);
        return new ChallengeResource($challenge);
        /*$challenge = Challenge::select([
                'challenges.id',
                'challenges.program',
                'challenges.name',
                'challenges.description',
                'challenges.reward'])
            ->with(['attachments:id,path,original_name,size,attachable_id,attachable_type'])
            ->find($id);
        return response()->json(['vyzvy' => $challenge], Response::HTTP_OK);
        /*$challenge = Challenge::find($id);
        return response()->json(['vyzva' => $challenge], Response::HTTP_OK);*/
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
