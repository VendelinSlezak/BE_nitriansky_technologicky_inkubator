<?php

namespace App\Http\Controllers;

use App\Http\Resources\TeamResource;
use App\Models\Team;
use App\Models\Student;
use App\Models\Challenge;
use App\Models\File;
use App\Services\FileService;
use App\Events\StudentInvited;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Throwable;
use Exception;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teams = Team::all();

        return response()->json(['teams' => TeamResource::collection($teams)], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage (Registrácia tímu študentom cez e-maily).
     */
    public function store(Request $request, FileService $fileService)
    {
        if (auth()->user()->student->can_be_invited() === false) {
            return response()->json(['error' => 'You are not allowed to create a team'], Response::HTTP_UNAUTHORIZED);
        }

        $validated = $request->validate([
            'challenge_id' => 'required|exists:challenges,id',
            'name_of_team' => 'required|string',
            'members' => 'sometimes|array',
            'members.*.email' => 'required_with:members|email|exists:users,email',
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
                            // Zachované teamMembers()
                            $team->teamMembers()->sync($syncData);

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
        } catch (Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        $team->load('teamMembers');

        return response()->json(new TeamResource($team));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage (Vymazanie tímu).
     */
    public function destroy(Team $team, FileService $fileService)
    {
        if (!auth()->user()->isAdmin()) {
            $teamleader = $team->teamMembers()->wherePivot('status', 'teamleader')->first();
            if (!$teamleader || auth()->user()->student->id !== $teamleader->id) {
                return response()->json([
                    'message' => 'Only teamleader can delete the team'
                ], Response::HTTP_FORBIDDEN);
            }
        }

        foreach ($team->teamMembers as $student) {
            if ($student->pivot->statuory_declaration_id) {
                $file = File::find($student->pivot->statuory_declaration_id);
                if ($file) {
                    $fileService->deleteFile($file);
                }
            }
        }

        $team->teamMembers()->detach();
        $team->delete();

        return response()->json(['message' => 'Team deleted successfully'], Response::HTTP_OK);
    }

    /**
     * Manuálne vytvorenie tímu (Napr. administrátorom cez IDčká užívateľov)
     */
    public function createTeam(Request $request, FileService $fileService)
    {
        $validated = $request->validate([
            'challenge_id' => 'required|exists:challenges,id',
            'name_of_team' => 'required|string',
            'members' => 'required|array|min:1',
            'members.*.id' => 'required|exists:students,id',
            'members.*.status' => 'required|in:member,teamleader',
            'proposal_of_implementation' => 'required|file',
            'cover_letter' => 'required|file',
        ]);

        try {
            DB::transaction(function () use ($validated, $request, $fileService) {
                $proposal_of_implementation = $fileService->uploadAndCreateRecord(
                    file: $request->file('proposal_of_implementation'),
                    subFolder: 'challenges/documents',
                    disk: 'private'
                );

                $cover_letter = $fileService->uploadAndCreateRecord(
                    file: $request->file('cover_letter'),
                    subFolder: 'challenges/documents',
                    disk: 'private'
                );

                $team = Team::create([
                    'challenge_id' => $validated['challenge_id'],
                    'name' => $validated['name_of_team'],
                    'active_from' => now(),
                    'active_to' => null,
                    'proposal_of_implementation_id' => $proposal_of_implementation->id,
                    'cover_letter_id' => $cover_letter->id,
                    'status' => 'active'
                ]);

                $studentIds = collect($validated['members'])->pluck('id')->toArray();
                $studentsMap = Student::whereIn('id', $studentIds)->get()->keyBy('id');

                $membersData = [];
                foreach ($validated['members'] as $member) {
                    $student = $studentsMap->get($member['id']);
                    
                    if ($student) {
                        $dbStatus = $member['status'] === 'member' ? 'team_member' : 'teamleader';

                        $membersData[$student->id] = [
                            'status' => $dbStatus,
                            'active_from' => now(),
                        ];
                        
                        $student->update([
                            'team_status' => $dbStatus
                        ]);
                    }
                }

                $team->teamMembers()->attach($membersData);
            });

            return response()->json(['message' => 'Tím bol úspešne vytvorený'], Response::HTTP_OK);

        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateTeam(Team $team, Request $request, FileService $fileService)
    {
        $validated = $request->validate([
            'challenge_id' => 'nullable|exists:challenges,id',
            'name_of_team' => 'nullable|string',
            'members' => 'nullable|array|min:1',
            'members.*.id' => 'required_with:members|exists:students,id',
            'members.*.status' => 'required_with:members|in:member,teamleader',
            'proposal_of_implementation' => 'nullable|file',
            'cover_letter' => 'nullable|file',
        ]);

        $filledData = array_filter($validated, function ($value) {
            return !is_null($value);
        });

        $teamData = Arr::only($filledData, ['challenge_id', 'name_of_team']);

        if (array_key_exists('name_of_team', $teamData)) {
            $teamData['name'] = $teamData['name_of_team'];
            unset($teamData['name_of_team']);
        }

        $oldProposalId = null;
        $oldCoverLetterId = null;

        if ($request->hasFile('proposal_of_implementation')) {
            $oldProposalId = $team->proposal_of_implementation_id;

            $proposal = $fileService->uploadAndCreateRecord(
                file: $request->file('proposal_of_implementation'),
                subFolder: 'challenges/documents',
                disk: 'public'
            );

            $teamData['proposal_of_implementation_id'] = $proposal->id;
        }

        if ($request->hasFile('cover_letter')) {
            $oldCoverLetterId = $team->cover_letter_id;

            $coverLetter = $fileService->uploadAndCreateRecord(
                file: $request->file('cover_letter'),
                subFolder: 'challenges/documents',
                disk: 'public'
            );

            $teamData['cover_letter_id'] = $coverLetter->id;
        }

        if (!empty($teamData)) {
            $team->update($teamData);
        }

        if ($oldProposalId || $oldCoverLetterId) {
            try {
                DB::transaction(function () use ($oldProposalId, $oldCoverLetterId) {
                    if ($oldProposalId) {
                        File::destroy($oldProposalId);
                    }

                    if ($oldCoverLetterId) {
                        File::destroy($oldCoverLetterId);
                    }
                });
            } catch (Throwable $e) {
                return response()->json([
                    'message' => 'Something went wrong while deleting old files: ' . $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        if (array_key_exists('members', $filledData)) {
            $syncData = [];

            if (is_array($filledData['members'])) {
                foreach ($filledData['members'] as $member) {
                    $syncData[$member['id']] = [
                        'status' => $member['status'],
                    ];
                }
            }
            $team->teamMembers()->sync($syncData);
        }
        return response()->json(['message' => 'Team updated successfully'], Response::HTTP_OK);
    }
    /**
     * Pozvanie dodatočného člena do tímu.
     */
    public function inviteMember(Request $request, Team $team)
    {
        $teamleader = $team->teamMembers()->wherePivot('status', 'teamleader')->first();
        if (!$teamleader || auth()->user()->student->id !== $teamleader->id) {
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

                $isAlreadyInTeam = $team->teamMembers()->where('student_id', $student->id)->exists();
                if ($isAlreadyInTeam) {
                    return response()->json([
                        'error' => "Student {$email} is already a member or has been invited to this team"
                    ], Response::HTTP_CONFLICT);
                }

                $team->teamMembers()->attach($student->id, [
                    'status' => 'invited'
                ]);

                event(new StudentInvited($email, $team));

                return response()->json([
                    'message' => 'Member invited successfully'
                ], Response::HTTP_OK);
            });
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Odobranie člena z tímu.
     */
    public function deleteMember(Request $request, Team $team, Student $student, FileService $fileService)
    {
        $teamleader = $team->teamMembers()->wherePivot('status', 'teamleader')->first();
        if (!$teamleader || auth()->user()->student->id !== $teamleader->id) {
            return response()->json([
                'message' => 'Only teamleader can delete members'
            ], Response::HTTP_FORBIDDEN);
        }

        $memberPivot = $team->teamMembers()->where('student_id', $student->id)->first();
        if ($memberPivot && $memberPivot->pivot->statuory_declaration_id) {
            $file = File::find($memberPivot->pivot->statuory_declaration_id);
            if ($file) {
                $fileService->deleteFile($file);
            }
        }

        $team->teamMembers()->detach($student->id);

        return response()->json([
            'message' => 'Člen bol úspešne odobraný z tímu.'
        ], Response::HTTP_OK);
    }

    /**
     * Odoslanie tímu na schválenie.
     */
    public function completeTeam(Team $team)
    {
        $teamleader = $team->teamMembers()->wherePivot('status', 'teamleader')->first();
        if (!$teamleader || auth()->user()->student->id !== $teamleader->id) {
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
