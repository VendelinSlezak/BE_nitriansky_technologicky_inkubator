<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChallengeResource;
use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\FileService;
use Throwable;
use App\Models\File;
use App\Events\ProgramAChallengeProposed;

class ChallengeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $challenges = Challenge::with('program_a_categories')->get();
        return ChallengeResource::collection($challenges);
    }

    public function getProgramAChallenges() {
        $challenges = Challenge::with('program_a_categories')
            ->where('program', 'A')
            ->get()
            ->map(function ($company) {
                return [
                    'id' => $company->id,
                    'name' => $company->name,
                    'description' => $company->description,
                    'technical_specification' => $company->proposal_file->url,
                ];
            });
        return response()->json($challenges, Response::HTTP_OK);
    }

    public function getProgramBChallenges() {
        $challenges = Challenge::with('program_a_categories')
            ->where('program', 'B')
            ->get()
            ->map(function ($company) {
                return [
                    'id' => $company->id,
                    'name' => $company->name,
                    'description' => $company->description,
                    'reward' => $company->reward,
                    'technical_specification' => $company->proposal_file->url,
                ];
            });
        return response()->json($challenges, Response::HTTP_OK);
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
        $challenge = Challenge::with(['files', 'program_a_categories'])->findOrFail($id);
        return new ChallengeResource($challenge);
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

    public function getThreeRandomChallenges() {
        $challenges = Challenge::select('id', 'program', 'name', 'description', 'reward', 'program_a_category_id')
            ->with('program_a_categories')
            ->inRandomOrder()
            ->limit(3)
            ->get()
            ->map(function ($challenge) {
                if($challenge->program == 'A') {
                    return [
                        'id' => $challenge->id,
                        'program' => 'A',
                        'title' => $challenge->name,
                        'description' => $challenge->description,
                        'category' => $challenge->program_a_categories->title,
                    ];
                }
                else {
                    return [
                        'id' => $challenge->id,
                        'program' => 'B',
                        'title' => $challenge->name,
                        'description' => $challenge->description,
                        'reward' => $challenge->reward
                    ];
                }
            });

        return response()->json($challenges, Response::HTTP_OK);
    }

    public function createProgramAChallenge(Request $request, FileService $fileService) {
        $validated = $request->validate([
            'name_of_challenge' => 'required|string',
            'category_of_challenge_id' => 'required|string',
            'description_of_challenge' => 'required|string',
            'automatically_became_leader' => 'required|boolean',
            'proposal_implemenation_file' => 'required|file|max:8192',
        ]);

        try {
            $file = $fileService->uploadAndCreateRecord(
                $request->file('proposal_implemenation_file'), 
                'proposal_implementation',
                'private',
                function (File $fileRecord) use ($validated) {
                    $challenge = Challenge::create([
                        'program' => 'A',
                        'user_id' => auth()->user()->id,
                        'name' => $validated['name_of_challenge'],
                        'automatically_create_team_after_approval' => $validated['automatically_became_leader'],
                        'description' => $validated['description_of_challenge'],
                        'status' => 'proposed',
                        'program_a_category_id' => $validated['category_of_challenge_id'],
                        'proposal_file_id' => $fileRecord->id,
                    ]);

                    event(new ProgramAChallengeProposed($challenge));
                }
            );

            return response()->json([
                'message' => 'Výzva bola úspešne podaná.',
            ], Response::HTTP_CREATED);
        }
        catch (Throwable $e) {
            return response()->json([
                'message' => 'Registrácia výzvy zlyhala. Skúste to neskôr.',
                'error' => config('app.debug') ? $e->getMessage() : null // Debug info len pre vývoj
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function adminChallengesInfo() {
        $challenges = Challenge::with('teams')->get();
        return ChallengeResource::collection($challenges);
    }
}