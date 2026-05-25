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
use App\Events\ProgramBChallengeProposed;
use App\Models\Milestone;

class ChallengeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $challenges = Challenge::with('program_a_categories')->where('status', 'open')->get();
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
            ->where('status', 'open')
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
            'proposal_implemenation_file' => 'required|file|max:8192',
        ]);

        try {
            $fileService->uploadAndCreateRecord(
                $request->file('proposal_implemenation_file'),
                'proposal_implementation',
                'private',
                function (File $fileRecord) use ($validated) {
                    $challenge = Challenge::create([
                        'program' => 'A',
                        'user_id' => auth()->user()->id,
                        'name' => $validated['name_of_challenge'],
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

    public function createProgramBChallenge(Request $request, FileService $fileService) {
        $validated = $request->validate([
            'name_of_challenge' => 'required|string',
            'description_of_challenge' => 'required|string',
            'reward' => 'required|decimal:0,2|min:0|max:100000',
            'documentation_file' => 'required|file|max:8192',
            'product_owner_id' => 'required|exists:users,id',
        ]);
        $company = auth()->user()->company;
        if(!$company->company_employees()->where('user_id', $validated['product_owner_id'])->exists()) {
            return response()->json([
                'message' => 'Product owner does not belong to company',
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            $fileService->uploadAndCreateRecord(
                $request->file('documentation_file'),
                'documentation',
                'private',
                function (File $fileRecord) use ($validated) {
                    $challenge = Challenge::create([
                        'program' => 'B',
                        'user_id' => auth()->user()->id,
                        'name' => $validated['name_of_challenge'],
                        'description' => $validated['description_of_challenge'],
                        'reward' => $validated['reward'],
                        'status' => 'proposed',
                        'proposal_file_id' => $fileRecord->id,
                        'product_owner_id' => $validated['product_owner_id'],
                    ]);

                    event(new ProgramBChallengeProposed($challenge));
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
            ]);
        }
    }

    public function adminChallengesInfo() {
        $challenges = Challenge::with('teams')->get();
        return ChallengeResource::collection($challenges);
    }

    public function getFullChallengeInfo(Challenge $challenge) {
        $this->authorize('view', $challenge);

        $response['id'] = $challenge->id;
        $response['program'] = $challenge->program;
        $response['name_of_challenge'] = $challenge->name;
        $response['challenge_description'] = $challenge->description;
        if($challenge->program == 'A') {
            $response['category'] = $challenge->program_a_categories->title;
            $response['category_skills'] = $challenge->program_a_categories->description_of_skills;
        }
        else {
            $response['reward'] = $challenge->reward;
        }
        $response['proposal_file'] = [
            'url' => $challenge->proposal_file->url,
            'name' => $challenge->proposal_file->original_name,
        ];
        $response['implementation_file'] = [
            'url' => $challenge->attached_team->proposal_of_implementation->url,
            'name' => $challenge->attached_team->proposal_of_implementation->original_name,
        ];
        $response['name_of_team'] = $challenge->attached_team->name;
        $response['team_members'] = $challenge->attached_team->students->map(function ($teamMember) {
            return [
                'id' => $teamMember->user->id,
                'name' => $teamMember->user->name,
                'email' => $teamMember->user->email,
                'status' => $teamMember->pivot->status,
            ];
        });
        $response['milestones'] = $challenge->milestones;
        if($challenge->status === 'accepted_by_commission') {
            $response['decision'] = [
                'status' => "accepted",
                'commission_comment' => $challenge->commission_comment,
                'mentor_id' => $challenge->mentor_id,
                'mentor_email' => $challenge->mentors->user->email
            ];
        }
        if($challenge->status === 'rejected_by_commission') {
            $response['decision'] = [
                'status' => "rejected",
                'commission_comment' => $challenge->commission_comment,
                'mentor_id' => null,
            ];
        }

        return response()->json($response, Response::HTTP_OK);
    }

    public function setMilestoneComment(Request $request, Challenge $challenge, Milestone $milestone) {
        $this->authorize('updateMilestone', $challenge);

        if($challenge->milestones()->where('id', $milestone->id)->doesntExist()) {
            return response()->json([
                'message' => 'Milestone not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'comment' => 'required|string',
        ]);

        $milestone->update([
            'comment' => $validated['comment'],
        ]);

        return response()->json([
            'message' => 'Comment on milestone updated successfully',
        ], Response::HTTP_OK);
    }

    public function setCommissionDecision(Request $request, Challenge $challenge) {
        $this->authorize('updateCommissionDecision', $challenge);

        $validated = $request->validate([
            'decision' => 'required|string|in:accepted,rejected',
            'comment' => 'required|string',
            'mentor_id' => 'required_if:decision,accepted|exists:mentors,id',
        ]);

        if($validated['decision'] == 'accepted') {
            $challenge->update([
                'status' => 'accepted_by_commission',
                'mentor_id' => $validated['mentor_id'],
            ]);
        }
        else {
            $challenge->update([
                'status' => 'rejected_by_commission',
                'mentor_id' => null,
            ]);
        }
        $challenge->update([
            'commission_comment' => $validated['comment'],
        ]);

        return response()->json([
            'message' => 'Commission decision updated successfully',
        ], Response::HTTP_OK);
    }

    public function updateMilestone(Request $request, Challenge $challenge, Milestone $milestone)
    {
        $validated = $request->validate([
            'date_of_completion' => 'required|date',
            'name' => 'required|string',
            'description' => 'required|string',
            'comment' => 'required|string',
        ]);

        $milestone->update([
            'title' => $validated['name'],
            'description' => $validated['description'],
            'comment' => $validated['comment'],
            'date_of_reasisation' => $validated['date_of_completion'],
        ]);

        return response('Milník bol úspešne aktualizovaný', Response::HTTP_OK);

    }

    public function addMilestone(Request $request, Challenge $challenge, Milestone $milestone, string $id) {
        $validated = $request->validate([
            'date_of_completion' => 'required|date',
            'name' => 'required|string',
            'description' => 'required|string'
            ]);

        $chal = $challenge->findOrFail($id);
        if ($chal) {
            $milestone->create([
                'challenge_id' => $id,
                'title' => $validated['name'],
                'description' => $validated['description'],
                'comment' => '',
                'date_of_reasisation' => $validated['date_of_completion'],
                'is_finished' => false
            ]);

            return response('Miľník bol úspešne pridaný', Response::HTTP_OK);
        }

    }

    public function destroyMilestone(Milestone $milestone)
    {
        $milestone->delete();
        return response('Miľník bol úspešne vymazaný', Response::HTTP_OK);
    }

    public function createChallenge(Request $request, Challenge $challenge, FileService $fileService) {
        $validated = $request->validate([
            'type' => 'required|string|in:A,B',
            'name' => 'required|string',
            'category_id' => 'required_if:type,A|exists:program_a_categories,id',
            'reward' => 'required_if:type,B|numeric',
            'description' => 'required|string',
            'technical_specification' => 'required|file'
        ]);

        $fileRecord = $fileService->uploadAndCreateRecord(
            file: $request->file('technical_specification'),
            subFolder: 'challenges/specifications',
            disk: 'public'
        );

        // 2. Teraz vytvoríme výzvu a priamo jej priradíme ID nového súboru.
        // Nahraď 'technical_specification_file_id' presným názvom stĺpca, ktorý máš v migrácii challenges.
        $newChallenge = $challenge->create([
            'program' => $validated['type'],
            'name' => $validated['name'],
            'program_a_category_id' => $validated['category_id'] ?? null,
            'reward' => $validated['reward'] ?? null,
            'description' => $validated['description'],
            'status' => 'open',
            'user_id' => 1,
            'proposal_file_id' => $fileRecord->id, // Týmto prepojíš výzvu so súborom
        ]);

        return response()->json([
            'message' => 'Výzva bola úspešne vytvorená spolu so špecifikáciou.',
            'challenge' => $newChallenge->load('file') // ak máš v modeli definovaný vzťah (belongsTo)
        ], Response::HTTP_CREATED);
    }

    public function acceptChallenge(Challenge $challenge)
    {
        $challenge->update([
            'status' => 'open'
        ]);

        return response('Výzva bola úspešne akceptovaná', Response::HTTP_OK);
    }

    public function closeChallenge(Request $request, Challenge $challenge) {
        $validated = $request->validate([
            'evaluation_of_challenge' => 'required|string'
        ]);

        $challenge->update([
            'status' => 'finished',
            'final_assessment' => $validated['evaluation_of_challenge']
        ]);

        return response('Výzva bola úspešne ukončená', Response::HTTP_OK);
    }

    public function destroyChallenge(Challenge $challenge, FileService $fileService)
    {
        $file = File::find($challenge->proposal_file_id);
        $challenge->delete();
        if ($file) {
            $fileService->deleteFile($file);
        }
        return response('Výzva bola zmazaná', Response::HTTP_OK);
    }
}
