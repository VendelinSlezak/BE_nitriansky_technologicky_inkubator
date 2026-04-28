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
use App\Models\Attachment;

class RegistrationController extends Controller
{
    public function registerStudent(Request $request) {
        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'university' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'gdpr' => 'required|accepted',
            'cv' => 'required|file|max:2048',
            'g-recaptcha-response' => ['required', new Recaptcha],
        ]);

        $filePath = null;
        $disk = 'public';
        try {
            return DB::transaction(function () use ($validated, $request, $disk, &$filePath) {
                $user = User::create([
                    'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                    'role' => 'student',
                ]);

                $cvFile = $request->file('cv');
                $directory = 'curriculum_vitae/users/' . $user->id;
                $filePath = $cvFile->store($directory, $disk);
                $newCV = Attachment::create([
                    'public_id' => (string) Str::ulid(),
                    'collection' => 'curriculum_vitae',
                    'visibility' => 'private',
                    'disk' => $disk,
                    'path' => $filePath,
                    'stored_name' => basename($filePath),
                    'original_name' => $cvFile->getClientOriginalName(),
                    'mime_type' => $cvFile->getMimeType(),
                    'size' => $cvFile->getSize(),
                    'attachable_id'   => 0, // Dočasná hodnota
                    'attachable_type' => Student::class,
                ]);

                $student = Student::create([
                    'user_id' => $user->id,
                    'university' => $validated['university'],
                    'curriculum_vitae_id' => $newCV->id,
                    'is_accepted_by_admin' => false,
                    'is_invited_to_the_team' => false,
                    'is_a_teamleader' => false,
                ]);

                $newCV->update([
                    'attachable_id' => $student->id,
                ]);

                event(new StudentRegistered($user, $student));

                return response()->json([
                    'message' => 'Študent bol úspešne zaregistrovaný.',
                ], Response::HTTP_CREATED);
            });
        }
        catch (Throwable $e) {
            if ($filePath && Storage::disk($disk)->exists($filePath)) {
                Storage::disk($disk)->delete($filePath);
            }

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

        $filePath = null;
        $disk = 'public';
        try {
            return DB::transaction(function () use ($validated, $request, $disk, &$filePath) {
                $user = User::create([
                    'name' => $validated['company_name'],
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                    'role' => 'company',
                ]);

                $logoFile = $request->file('logo');
                $directory = 'logos/users/' . $user->id;
                $filePath = $logoFile->store($directory, $disk);
                $newLogo = Attachment::create([
                    'public_id' => (string) Str::ulid(),
                    'collection' => 'logo',
                    'visibility' => 'public',
                    'disk' => $disk,
                    'path' => $filePath,
                    'stored_name' => basename($filePath),
                    'original_name' => $logoFile->getClientOriginalName(),
                    'mime_type' => $logoFile->getMimeType(),
                    'size' => $logoFile->getSize(),
                    'attachable_id'   => 0, // Dočasná hodnota
                    'attachable_type' => Company::class,
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
                    'logo_id' => $newLogo->id
                ]);

                $newLogo->update([
                    'attachable_id' => $company->id,
                ]);

                event(new CompanyRegistered($user, $company));

                return response()->json([
                    'message' => 'Firma bola úspešne zaregistrovaná.',
                ], Response::HTTP_CREATED);
            });
        }
        catch (Throwable $e) {
            if ($filePath && Storage::disk($disk)->exists($filePath)) {
                Storage::disk($disk)->delete($filePath);
            }

            return response()->json([
                'message' => 'Registrácia zlyhala. Skúste to neskôr.',
                'error' => config('app.debug') ? $e->getMessage() : null // Debug info len pre vývoj
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
