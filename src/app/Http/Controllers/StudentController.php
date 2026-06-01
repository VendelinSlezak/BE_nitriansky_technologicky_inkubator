<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProgramAResource;
use App\Http\Resources\StudentResource;
use App\Models\ProgramACategory;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\FileService;
use Throwable;
use App\Models\File;
use App\Models\User;

class StudentController extends Controller
{
    public function dashboard(Request $request) {
        $user = $request->user();
        $student = $user->student;

        $response['status'] = 'basic';
        $response['finished_projects'] = $student->teams()
            ->whereRelation('challenge', 'status', 'finished')
            ->get()
            ->map(function ($team) {
                return [
                    'id' => $team->challenge->id,
                    'name' => $team->challenge->name,
                    'date_of_completion' => $team->challenge->date_of_completion,
                    'final_assessment' => $team->challenge->final_assessment,
                ];
            });

        $active_team = $student->active_team()->first();
        if($active_team) {
            $challenge = $active_team->challenge;
            $response['name_of_team'] = $active_team->name;
            $response['project_program'] = $challenge->program;
            $response['name_of_project'] = $challenge->name;
            $response['description_of_project'] = $challenge->description;
            $response['team_id'] = $active_team->id;
            $response['team_members'] = $active_team->students->map(function ($teamMember) {
                if($teamMember->pivot->status == 'teamleader') {
                    $role = 'teamleader';
                    $status = 'active';
                }
                else if($teamMember->pivot->status == 'team_member') {
                    $role = 'member';
                    $status = 'active';
                }
                else {
                    $role = 'member';
                    $status = 'invited';
                }
                return [
                    'student_id' => $teamMember->id,
                    'name' => $teamMember->user->name,
                    'email' => $teamMember->user->email,
                    'role' => $role,
                    'status' => $status,
                ];
            });

            if($active_team->pivot->status == 'invited') {
                $response['status'] = 'invited';
                if($challenge->program === 'A') {
                    $program_a_category = $challenge->program_a_categories;
                    $response['category_name'] = $program_a_category->title;
                    $response['description_of_skills'] = $program_a_category->description_of_skills;
                }
            }
            else if($active_team->pivot->status == 'teamleader' && $active_team->status == 'draft') {
                $response['status'] = 'team_draft';
                $response['team_id'] = $active_team->id;
            }
            else { // team_member
                $response['status'] = 'team_waiting_for_approval';
                if($active_team->status == 'active') {
                    $response['status'] = 'approved_team';
                    $response['technical_specification_file'] = [
                        'url' => $active_team->technical_specification->url,
                        'name' => $active_team->technical_specification->original_name
                    ];
                    $response['proposal_of_implementation_file'] = [
                        'url' => $active_team->proposal_of_implementation->url,
                        'name' => $active_team->proposal_of_implementation->original_name
                    ];
                    $response['milestones'] = $challenge->milestones;
                }
            }
        }

        return response()->json($response);
    }

    public function adminStudentsInfo() {
        $students = Student::with('user')->get();
        return StudentResource::collection($students);
    }

    public function destroy(string $id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['message' => 'Student not found'], Response::HTTP_NOT_FOUND);
        }
        $student->delete();
        return response()->json(['message' => 'Student deleted successfully'], Response::HTTP_OK);
    }

    public function acceptTeamInvitation(Request $request, FileService $fileService) {
        $validate = $request->validate([
            'statuory_declaration' => 'required|file|max:2048',
        ]);

        $team = $request->user()->student->active_team()->first();
        if(!$team || $team->pivot->status != 'invited') {
            return response()->json([
                'message' => 'No active invitation found'
            ], Response::HTTP_NOT_FOUND);
        }

        try {
            $fileService->uploadAndCreateRecord(
                $request->file('statuory_declaration'),
                'statutory_declarations',
                'private',
                function (File $fileRecord) use ($request, $team) {
                    $student = $request->user()->student;
                    $student->teams()->updateExistingPivot($team->id, [
                        'status' => 'team_member',
                        'active_from' => now(),
                        'statuory_declaration_id' => $fileRecord->id,
                    ]);
                    $student->update([
                        'team_status' => 'team_member',
                    ]);
                }
            );

            return response()->json([
                'message' => 'Team invitation accepted successfully'
            ], Response::HTTP_OK);
        }
        catch (Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function rejectTeamInvitation(Request $request) {
        $student = $request->user()->student;
        $team = $student->active_team()->first();

        if(!$team || $team->pivot->status != 'invited') {
            return response()->json([
                'message' => 'No active invitation found'
            ], Response::HTTP_NOT_FOUND);
        }

        $student->teams()->detach($team->id);

        return response()->json([
            'message' => 'Team invitation rejected and membership removed'
        ], Response::HTTP_OK);
    }

    public function canBeInvited(Request $request) {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);
        $user = User::where('email', $validated['email'])->first();
        if($user->id == auth()->user()->id) {
            return response()->json([
                'status' => false
            ], Response::HTTP_OK);
        }
        $student = Student::where('user_id', $user->id)->first();
        return response()->json([
            'status' => $student->can_be_invited()
        ], Response::HTTP_OK);
    }

    public function getProgramACategories()
    {
        $categories = ProgramACategory::where('status', 'visible')->get();
        return response()->json([
            'categories' => ProgramAResource::collection($categories)
        ]);
    }

    public function getRegistrationRequests()
    {
        $students = Student::where('is_accepted_by_admin', false)
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'type' => 'student',
                    'name' => $student->user->name,
                    'email' => $student->user->email,
                    'university' => $student->university,
                    'curriculum_vitae_url' => $student->curriculumVitae->url,
                    'curriculum_vitae_name' => $student->curriculumVitae->original_name
                ];
            });
        
        return response()->json([
            'students' => $students
        ], Response::HTTP_OK);
    }

    public function approveRegistration(Student $student) {
        if($student->is_accepted_by_admin) {
            return response()->json([
                'message' => 'Registration request already approved'
            ], Response::HTTP_OK);
        }

        $student->is_accepted_by_admin = true;
        $student->save();
        return response()->json([
            'message' => 'Registration request approved'
        ], Response::HTTP_OK);
    }

    public function rejectRegistration(Student $student, FileService $fileService) {
        if($student->is_accepted_by_admin) {
            return response()->json([
                'message' => 'Registration request already approved'
            ], Response::HTTP_OK);
        }
        
        try {
            DB::transaction(function () use ($student, $fileService) {
                $curriculum_vitae = $student->curriculumVitae;
                $student->delete();
                $fileService->deleteFile($curriculum_vitae);
            });
        }
        catch (Throwable $e) {
            return response()->json([
                'message' => 'Internal server error',
                'error' => $e->getMessage() // for debug only
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'message' => 'Registration request rejected'
        ], Response::HTTP_OK);
    }
}
