<?php

namespace App\Http\Controllers;

use App\Http\Resources\MentorResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Mentor;
use App\Models\Challenge;

class MentorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mentors = Mentor::join('users', 'users.id', '=', 'mentors.user_id')
            ->select('users.name', 'mentors.experience', 'mentors.expertise')
            ->get();

        return response()->json(['mentori' => $mentors]);
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
        //
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

    public function adminMentorsInfo()
    {
        $mentor = Mentor::with('user')->get();
        return MentorResource::collection($mentor);
    }

    public function mentorChallengesInfo(Request $request) {
        $challenges = Challenge::where('mentor_id', auth()->user()->mentor->id)
            ->whereNull('final_assessment')
            ->with('attached_team')
            ->get()
            ->map(fn($challenge) => [
                'id' => $challenge->id,
                'program' => $challenge->program,
                'name_of_challenge' => $challenge->name,
                'name_of_team' => $challenge->attached_team->name ?? null,
            ]);

        return response()->json($challenges, Response::HTTP_OK);
    }
}
