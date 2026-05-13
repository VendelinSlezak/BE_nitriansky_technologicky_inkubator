<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommissionMemberController extends Controller
{
    public function index() {
        $members = User::where('role', 'committee_member')->get();

        return response()->json([
            'committee_members' => UserResource::collection($members)
        ]);
    }
    public function commissionMemberChallengesInfo(Request $request) {
        $challenges = auth()->user()->commision_member_challenges()
            ->where('challenges.status', 'in_evaluation')
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
