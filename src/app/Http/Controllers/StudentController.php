<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function dashboard(Request $request) {
        $user = $request->user();
        $student = $user->student;
        
        $response = [];
        if($student->is_invited_to_the_team()) {
            $response['status'] = 'invited';
        }
        else if($student->is_member_of_the_team()) {
            $response['status'] = 'member_of_team';
        }
        else {
            $response['status'] = 'basic';
        }

        return response()->json($response);
    }
}
