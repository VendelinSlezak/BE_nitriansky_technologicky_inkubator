<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\Request;

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

            if($active_team->status == 'invited') {
                $response['status'] = 'invited';
                if($challenge->program == 'A') {
                    $response['category_name'] = $challenge->program_a_category->title;
                    $response['description_of_skills'] = $challenge->program_a_category->description_of_skills;
                }
                $response['link_to_statutory_declaration'] = $$challenge->program_a_category->statutory_declaration->url;
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
}
