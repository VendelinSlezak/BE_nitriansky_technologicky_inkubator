<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rules\Recaptcha;
use App\Models\User;
use App\Models\Student;
use App\Events\StudentRegistered;
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
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
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
}
