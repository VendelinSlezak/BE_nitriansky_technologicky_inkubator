<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function dashboard(Request $request) {
        $user = $request->user();
        $student = $user->student;
        
        $response['status'] = 'basic';

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
                    $response['description_of_skills'] = $challenge->program_a_category->description_of_skills;
                }
                // TODO: link_to_statutory_declaration
            }
            else {
                $response['status'] = 'member_of_team';
                if($active_team->active_from == null) {
                    $response['status_of_team'] = 'waiting';
                }
                else {
                    $response['status_of_team'] = 'approved';
                    // TODO: technical_specification
                    // TODO: proposal_of_implementation
                    $response['milestones'] = $challenge->milestones;
                }
            }
        }

        return response()->json($response);
    }
}
