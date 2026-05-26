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
use App\Events\StudentInvited;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

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
        if (auth()->user()->student->can_be_invited() === false) {
            return response()->json(['error' => 'You are not allowed to create a team'], Response::HTTP_UNAUTHORIZED);
        }

        $validated = $request->validate([
            'challenge_id' => 'required|exists:challenges,id',
            'name_of_team' => 'required|string',
            'members' => 'sometimes|array', // Zmena z required na sometimes
            'members.*.email' => 'required_with:members|email|exists:users,email', // Kontrola mailov iba ak members existuje
            'proposal_of_implementation' => 'required|file|max:2048',
            'cover_letter' => 'required|file|max:2048',
        ]);

        $challenge = Challenge::findOrFail($validated['challenge_id']);
        $hasExactlyOneActiveTeam = $challenge->teams()
            ->whereNotNull('active_from')
            ->count() === 1;

        if (!$hasExactlyOneActiveTeam) {
            return response()->json(['error' => 'There is already an active team for this challenge'], Response::HTTP_CONFLICT);
        }

        try {
            return $fileService->uploadAndCreateRecord(
                $request->file('proposal_of_implementation'), 
                'proposals_of_implementation',
                'private',
                function (File $POIfileRecord) use ($fileService, $request, $validated) {
                    return $fileService->uploadAndCreateRecord(
                        $request->file('cover_letter'), 
                        'cover_letters',
                        'private',
                        function (File $CLfileRecord) use ($POIfileRecord, $validated) {
                            $currentUser = auth()->user();
                            
                            $members = Arr::get($validated, 'members', []);

                            $invitedEmails = collect($members)
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

                            foreach ($students as $student) {
                                if ($student->can_be_invited() == false) {
                                    throw new Exception("Student {$student->user->email} is not allowed to be invited");
                                }
                            }

                            $team = Team::create([
                                'name' => $validated['name_of_team'],
                                'status' => 'draft',
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

                            // Eventy sa spustia len pre reálne pozvaných ľudí (ak nejakí sú)
                            foreach ($invitedEmails as $email) {
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
    public function destroy(Team $team, FileService $fileService)
    {
        if(!auth()->user()->isAdmin()) {
            $teamleader = $team->teamMembers()->get()->where('status', 'teamleader')->first();
            if(auth()->user()->id !== $teamleader->student->user_id) {
                return response()->json([
                    'message' => 'Only teamleader can delete the team'
                ], Response::HTTP_FORBIDDEN);
            }
        }

        $team_members = $team->teamMembers()->get();
        foreach ($team_members as $team_member) {
            if ($team_member->statuory_declaration) {
                $fileService->deleteFile($team_member->statuory_declaration);
            }
        }
        $team->delete();
        return response()->json(['message' => 'Team deleted successfully'], Response::HTTP_OK);
    }

    public function inviteMember(Request $request, Team $team) {
        $teamleader = $team->teamMembers()->get()->where('status', 'teamleader')->first();
        if(auth()->user()->id !== $teamleader->student->user_id) {
            return response()->json([
                'message' => 'Only teamleader can invite members'
            ], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        $email = $validated['email'];

        try {
            return DB::transaction(function () use ($email, $team) {
                $student = Student::query()
                    ->join('users', 'students.user_id', '=', 'users.id')
                    ->where('users.email', $email)
                    ->select('students.*')
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($student->can_be_invited() === false) {
                    return response()->json([
                        'error' => "Student {$email} is not allowed to be invited"
                    ], Response::HTTP_BAD_REQUEST);
                }
                $isAlreadyInTeam = $team->students()->where('student_id', $student->id)->exists();
                if ($isAlreadyInTeam) {
                    return response()->json([
                        'error' => "Student {$email} is already a member or has been invited to this team"
                    ], Response::HTTP_CONFLICT);
                }

                $team->students()->attach($student->id, [
                    'status' => 'invited'
                ]);
                $student->update([
                    'team_status' => 'invited'
                ]);

                event(new StudentInvited($email, $team));

                return response()->json([
                    'message' => 'Member invited successfully'
                ], Response::HTTP_OK);
            });

        }
        catch (Throwable $e) {
            return response()->json([
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMember(Request $request, Team $team, Student $student, FileService $fileService) {
        $teamleader = $team->teamMembers()->get()->where('status', 'teamleader')->first();
        if(auth()->user()->id !== $teamleader->student->user_id) {
            return response()->json([
                'message' => 'Only teamleader can delete members'
            ], Response::HTTP_FORBIDDEN);
        }

        $statuory_declaration = $team->students()->where('id', $student->id)->first()->pivot->statuory_declaration;
        if($statuory_declaration) {
            $fileService->deleteFile($statuory_declaration);
        }
        $team->students()->detach($student->id);
        return response()->json([
            'message' => 'Člen bol úspešne odobraný z tímu.'
        ], Response::HTTP_OK);
    }

    public function completeTeam(Team $team) {
        $teamleader = $team->teamMembers()->get()->where('status', 'teamleader')->first();
        if(auth()->user()->id !== $teamleader->student->user_id) {
            return response()->json([
                'message' => 'Only teamleader can complete the team'
            ], Response::HTTP_FORBIDDEN);
        }

        $team->update([
            'status' => 'waiting_for_approval'
        ]);
        return response()->json([
            'message' => 'Team completed successfully'
        ], Response::HTTP_OK);
    }
}
