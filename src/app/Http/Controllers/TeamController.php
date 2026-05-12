<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\FileService;
use App\Models\Challenge;
use App\Models\User;
use App\Models\Student;
use Throwable;
use App\Models\File;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, FileService $fileService)
    {
        if(auth()->user()->student->can_be_invited() === false) {
            return response()->json(['error' => 'You are not allowed to create a team'], Response::HTTP_UNAUTHORIZED);
        }

        $validated = $request->validate([
            'challenge_id' => 'required|exists:challenges,id',
            'name_of_team' => 'required|string',
            'members' => 'required|array',
            'members.*.email' => 'required|email|exists:users,email',
            'proposal_of_implementation' => 'required|file|max:2048',
            'cover_letter' => 'required|file|max:2048',
        ]);

        $challenge = Challenge::findOrFail($validated['challenge_id']);
        $hasExactlyOneActiveTeam = $challenge->teams()
            ->whereNotNull('active_from')
            ->count() === 1;
        if(!$hasExactlyOneActiveTeam) {
            return response()->json(['error' => 'There is already an active team for this challenge'], Response::HTTP_CONFLICT);
        }

        try {
            $fileService->uploadAndCreateRecord(
                $request->file('proposal_of_implementation'), 
                'proposals_of_implementation',
                'private',
                function (File $POIfileRecord) use ($fileService, $request, $validated) {
                    $fileService->uploadAndCreateRecord(
                        $request->file('cover_letter'), 
                        'cover_letters',
                        'private',
                        function (File $CLfileRecord) use ($POIfileRecord, $validated) {
                            $currentUser = auth()->user();
                            $invitedEmails = collect($validated['members'])
                                ->pluck('email')
                                ->reject(fn($email) => $email === $currentUser->email)
                                ->unique();
                            $allEmails = collect($invitedEmails)->push($currentUser->email);

                            $students = Student::query()
                                ->join('users', 'students.user_id', '=', 'users.id')
                                ->whereIn('users.email', $allEmails)
                                ->select('students.*')
                                ->lockForUpdate()
                                ->get();
                            foreach($students as $student) {
                                if($student->can_be_invited() == false) {
                                    throw new Exception("Student {$student->user->email} is not allowed to be invited");
                                }
                            }

                            $team = Team::create([
                                'name' => $validated['name_of_team'],
                                'active_from' => null,
                                'active_to' => null,
                                'challenge_id' => $validated['challenge_id'],
                                'proposal_of_implementation_id' => $POIfileRecord->id,
                                'cover_letter_id' => $CLfileRecord->id,
                            ]);
                            $syncData = [];
                            foreach ($students as $student) {
                                $status = ($student->user_id === $currentUser->id) ? 'teamleader' : 'invited';
                                $syncData[$student->id] = [
                                    'status' => $status
                                ];
                            }
                            $team->students()->sync($syncData);

                            foreach($invitedEmails as $email) {
                                event(new StudentInvited($email, $team));
                            }

                            return response()->json([
                                'message' => 'Team created successfully'
                            ], Response::HTTP_OK);
                        }
                    );
                }
            );
        }
        catch (Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        $team = Team::find($id);
        if (!$team) {
            return response()->json(['message' => 'Team not found'], Response::HTTP_NOT_FOUND);
        }
        $team->delete();
        return response()->json(['message' => 'Team deleted successfully'], Response::HTTP_OK);
    }
}
