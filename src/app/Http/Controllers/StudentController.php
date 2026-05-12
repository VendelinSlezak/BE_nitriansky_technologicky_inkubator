<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\FileService;
use Throwable;
use App\Models\File;

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

            if($active_team->pivot->status == 'invited') {
                $response['status'] = 'invited';
                if($challenge->program === 'A') {
                    $program_a_category = $challenge->program_a_categories;
                    $response['category_name'] = $program_a_category->title;
                    $response['description_of_skills'] = $program_a_category->description_of_skills;
                }
                // $response['link_to_statutory_declaration'] = $challenge->pivot->program_a_categories->statutory_declaration->url; // TODO: lepšie vymyslieť odkaz na statutory declaration
            }
            else {
                $response['status'] = 'member_of_team';
                if($active_team->active_from == null) {
                    $response['status_of_team'] = 'waiting';
                }
                else {
                    $response['status_of_team'] = 'approved';
                    $response['technical_specification'] = $challenge->proposal_file->url;
                    $response['proposal_of_implementation'] = $active_team->proposal_of_implementation->url;
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
                        'status' => 'member_of_team',
                        'active_from' => now(),
                        'statuory_declaration_id' => $fileRecord->id,
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

    public function canBeInvited(Student $student) {
        return response()->json([
            'status' => $student->can_be_invited()
        ], Response::HTTP_OK);
    }
}
