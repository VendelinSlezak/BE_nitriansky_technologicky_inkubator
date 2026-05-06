<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rules\Recaptcha;
use App\Models\User;
use App\Models\Student;
use App\Models\Company;
use App\Events\StudentRegistered;
use App\Events\CompanyRegistered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use App\Services\FileService;
use App\Models\File;

class RegistrationController extends Controller
{
    public function registerStudent(Request $request, FileService $fileService) {
        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'university' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'gdpr' => 'required|accepted',
            'cv' => 'required|file|max:2048',
            // 'g-recaptcha-response' => ['required', new Recaptcha],
        ]);

        try {
            $file = $fileService->uploadAndCreateRecord(
                $request->file('cv'), 
                'curriculum_vitaes',
                'private',
                function (File $fileRecord) use ($validated) {
                    $user = User::create([
                        'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                        'email' => $validated['email'],
                        'password' => $validated['password'],
                        'role' => 'student',
                    ]);

                    $student = Student::create([
                        'user_id' => $user->id,
                        'university' => $validated['university'],
                        'curriculum_vitae_id' => $fileRecord->id,
                        'is_accepted_by_admin' => false,
                        'team_status' => "not_in_team",
                    ]);

                    event(new StudentRegistered($user, $student));
                }
            );

            return response()->json([
                'message' => 'Študent bol úspešne zaregistrovaný.',
            ], Response::HTTP_CREATED);
        }
        catch (Throwable $e) {
            return response()->json([
                'message' => 'Registrácia zlyhala. Skúste to neskôr.',
                'error' => config('app.debug') ? $e->getMessage() : null // Debug info len pre vývoj
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function registerCompany(Request $request) {
        $validated = $request->validate([
            'company_name' => 'required',
            'company_address' => 'required',
            'description' => 'required',
            'category' => 'required',
            'ico' => 'required|numeric|digits:8',
            'dic' => 'required|numeric|digits:10',
            'name_of_contact_person' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'logo' => 'required|file|max:2048',
            'gdpr' => 'required|accepted',
            // 'g-recaptcha-response' => ['required', new Recaptcha],
        ]);

        try {
            $file = $fileService->uploadAndCreateRecord(
                $request->file('cv'), 
                'company_logos', 
                'public',
                function (File $fileRecord) use ($validated) {
                    $user = User::create([
                        'name' => $validated['company_name'],
                        'email' => $validated['email'],
                        'password' => $validated['password'],
                        'role' => 'company',
                    ]);

                    $company = Company::create([
                        'user_id' => $user->id,
                        'company_name' => $validated['company_name'],
                        'company_address' => $validated['company_address'],
                        'description' => $validated['description'],
                        'category' => $validated['category'],
                        'ico' => $validated['ico'],
                        'dic' => $validated['dic'],
                        'name_of_contact_person' => $validated['name_of_contact_person'],
                        'is_approved_by_admin' => false,
                        'show_logo_image' => true,
                        'logo_id' => $fileRecord->id,
                    ]);

                    event(new StudentRegistered($user, $student));
                }
            );

            return response()->json([
                'message' => 'Firma bola úspešne zaregistrovaná.',
            ], Response::HTTP_CREATED);
        }
        catch (Throwable $e) {
            return response()->json([
                'message' => 'Registrácia zlyhala. Skúste to neskôr.',
                'error' => config('app.debug') ? $e->getMessage() : null // Debug info len pre vývoj
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
