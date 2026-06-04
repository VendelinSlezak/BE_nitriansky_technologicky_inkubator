<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\File;
use App\Models\Mentor;
use App\Models\Student;
use App\Models\User;
use App\Services\FileService;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index() {
        return User::with(['student', 'company'])->get()->filter(function ($user) {
            if ($user->role == 'student') {
                return $user->student && $user->student->is_accepted_by_admin;
            }

            if ($user->role == 'company_admin') {
                return $user->company && $user->company->is_approved_by_admin;
            }

            return true;
        })->map(function ($user) {
            $data = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ];

            $data['role'] = match ($user->role) {
                'student'          => 'študent',
                'company_admin'    => 'firma',
                'mentor'           => 'mentor',
                'committee_member' => 'člen komisie',
                'admin'            => 'admin',
                'company_member'   => 'člen firmy',
                'web_editor'       => 'web editor',
                default            => $user->role,
            };

            return $data;
        })->values();
    }

    public function getUserAccount(Request $request, User $user) {
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role
        ];
        if($user->role == 'student') {
            $data['university'] = $user->student->university;
        }
        if($user->role == 'company_admin') {
            $data['ico'] = $user->company->ico;
            $data['dic'] = $user->company->dic;
            $data['contactPerson'] = $user->company->name_of_contact_person;
            $data['address'] = $user->company->address;
            $data['category'] = $user->company->category;
            $data['companyDescription'] = $user->company->description;
        }
        if($user->role == 'mentor') {
            $data['description'] = $user->mentor->description;
            $data['expertise'] = $user->mentor->expertise;
            $data['experience'] = $user->mentor->experience;
        }
        return $data;
    }

    public function updateUserAccount(Request $request, User $user, FileService $fileService) {
        $validated = $request->validate([
            'name' => 'nullable|string',
            'email' => [
                'nullable',
                'string',
                'email',
                Rule::unique('users')->ignore($request->route('user'))
            ],

            'university' => 'nullable|string',
            'curriculum_vitae' => ['nullable', 'file'],

            'ico' => 'nullable|string',
            'dic' => 'nullable|string',
            'name_of_contact_person' => 'nullable|string',
            'logo' => ['nullable', 'file'],
            'category' => 'nullable|string',
            'company_name' => 'nullable|string',
            'company_address' => 'nullable|string',

            'description' => ['nullable', 'string'],

            'expertise' => ['nullable', 'string'],
            'experience' => ['nullable', 'string'],
        ]);

        // Funkcia array_filter odstráni z poľa všetky kľúče, ktoré sú null alebo prázdne textové reťazce
        $filledData = array_filter($validated, function ($value) {
            return $value !== null && $value !== '';
        });

        // --- 1. AKTUALIZÁCIA POUŽÍVATEĽA (User) ---
        $userData = [];
        if (isset($filledData['name'])) $userData['name'] = $filledData['name'];
        if (isset($filledData['email'])) $userData['email'] = $filledData['email'];
        if (!empty($userData)) {
            $user->update($userData);
        }

        // --- 2. AKTUALIZÁCIA ŠTUDENTA ----
        if ($user->role === 'student') {
            $student = $user->student;
            $studentData = [];
            $fileToDelete = null; // Tu si len zapamätáme starý súbor na zmazanie

            if (isset($filledData['university'])) {
                $studentData['university'] = $filledData['university'];
            }

            if ($request->hasFile('curriculum_vitae')) {
                // 1. Nájdeme starý súbor, ale EŠTE HO NEMAŽEME
                if ($student->curriculum_vitae_id) {
                    $fileToDelete = File::find($student->curriculum_vitae_id);
                }

                // 2. Nahráme nový súbor
                $newFileId = $fileService->uploadAndCreateRecord(
                    file: $request->file('curriculum_vitae'),
                    subFolder: 'cv'
                );

                // 3. Pripravíme nové ID do poľa pre update
                $studentData['curriculum_vitae_id'] = $newFileId->id;
            }

            // 4. SPUSTÍME UPDATE ŠTUDENTA (Zápis do DB)
            // Toto zbehne, ak sa zmenila univerzita ALEBO ak sa nahral nový súbor
            if (!empty($studentData)) {
                $student->update($studentData);
            }

            // 5. BEZPEČNÉ MAZANIE: Starý súbor vymažeme až TERAZ,
            // pretože v databáze už študent ukazuje na nové curriculum_vitae_id
            if ($fileToDelete) {
                $fileService->deleteFile($fileToDelete);
            }

            return response('Študent účet bol úspešne aktualizovaný', Response::HTTP_OK);
        }

        // --- 3. AKTUALIZÁCIA FIRMY (Company) ---
        elseif ($user->role === 'company_admin') {
            $company = $user->company;
            $companyData = [];
            $fileToDelete = null; // Tu si len zapamätáme staré logo na zmazanie

            if (isset($filledData['company_name'])) $companyData['company_name'] = $filledData['company_name'];
            if (isset($filledData['company_address'])) $companyData['company_address'] = $filledData['company_address'];
            if (isset($filledData['description'])) $companyData['description'] = $filledData['description'];
            if (isset($filledData['ico'])) $companyData['ico'] = $filledData['ico'];
            if (isset($filledData['dic'])) $companyData['dic'] = $filledData['dic'];
            if (isset($filledData['category'])) $companyData['category'] = $filledData['category'];
            if (isset($filledData['name_of_contact_person'])) {
                $companyData['name_of_contact_person'] = $filledData['name_of_contact_person'];
            }

            if ($request->hasFile('logo')) {
                // 1. Nájdeme staré logo, ale EŠTE HO NEMAŽEME
                if ($company->logo_id) {
                    $fileToDelete = File::find($company->logo_id);
                }

                // 2. Nahráme nové logo
                $newFileId = $fileService->uploadAndCreateRecord(
                    file: $request->file('logo'),
                    subFolder: 'logos'
                );

                // 3. Pripravíme nové ID loga do poľa pre update
                $companyData['logo_id'] = $newFileId->id;
            }

            // 4. SPUSTÍME UPDATE FIRMY (Zápis do DB)
            // Toto zbehne, ak sa zmenilo ICO, DIC, meno osoby ALEBO sa nahralo nové logo
            if (!empty($companyData)) {
                $company->update($companyData);
            }

            // 5. BEZPEČNÉ MAZANIE: Staré logo vymažeme až po úspešnom update
            if ($fileToDelete) {
                $fileService->deleteFile($fileToDelete);
            }

            // 6. ODPOVEĎ VONKU: Vráti sa vždy, keď je to firma (aj keď sa menil len email/meno)
            return response('Company účet bol úspešne aktualizovaný', Response::HTTP_OK);
        }

        elseif ($user->role === 'mentor') {
            $mentor = $user->mentor;
            $mentorData = [];
            if (isset($filledData['description'])) {
                $mentorData['description'] = $filledData['description'];
            }
            if (isset($filledData['expertise'])) {
                $mentorData['expertise'] = $filledData['expertise'];
            }
            if (isset($filledData['experience'])) {
                $mentorData['experience'] = $filledData['experience'];
            }
            if (!empty($mentorData)) {
                $mentor->update($mentorData);
                return response('Mentor účet bol úspešne aktualizovaný', Response::HTTP_OK);
            }
        }
    }

    public function createAccount(Request $request, FileService $fileService)
    {
        $validated = $request->validate([
            'type' => 'required|in:student,company_admin,web_editor,mentor,committee_member',
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string',

            'university' => 'required_if:type,student|string',
            'curriculum_vitae' => 'required_if:type,student|file|mimes:pdf,docx,doc|max:5120',

            'description' => 'required_if:type,company_admin,mentor|string',

            'company_name' => 'required_if:type,company_admin|string',
            'company_address' => 'required_if:type,company_admin|string',
            'ico' => 'required_if:type,company_admin|string',
            'dic' => 'required_if:type,company_admin|string',
            'category' => 'required_if:type,company_admin|string',
            'name_of_contact_person' => 'required_if:type,company_admin|string',
            'logo' => 'required_if:type,company_admin|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            'expertise' => 'required_if:type,mentor|string',
            'experience' => 'required_if:type,mentor|string',
        ]);

        switch ($validated['type']) {
            case 'student':
                DB::transaction(function () use ($validated, $fileService, $request) {
                    $user = User::create([
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                        'password' => Hash::make($validated['password']),
                        'role' => $validated['type'],
                    ]);

                    $cv = $fileService->uploadAndCreateRecord(
                        file: $request->file('curriculum_vitae'),
                        subFolder: 'documents',
                        disk: 'private'
                    );

                    Student::create([
                        'user_id' => $user->id,
                        'university' => $validated['university'],
                        'curriculum_vitae_id' => $cv->id,
                        'is_accepted_by_admin' => true,
                        'team_status' => 'not_in_team',
                    ]);
                });
                break;

            case 'company':
                DB::transaction(function () use ($validated, $fileService, $request) {
                    $user = User::create([
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                        'password' => Hash::make($validated['password']),
                        'role' => $validated['type'],
                    ]);


                    $logo = $fileService->uploadAndCreateRecord(
                        file: $request->file('logo'),
                        subFolder: 'logos',
                        disk: 'private'
                    );

                    Company::create([
                        'company_name' => $validated['company_name'],
                        'company_address' => $validated['company_address'],
                        'description' => $validated['description'],
                        'ico' => $validated['ico'],
                        'dic' => $validated['dic'],
                        'category' => $validated['category'],
                        'name_of_contact_person' => $validated['name_of_contact_person'],
                        'is_approved_by_admin' => true,
                        'user_id' => $user->id,
                        'logo_id' => $logo->id,
                    ]);
                });
                break;

            case 'mentor':
                DB::transaction(function () use ($validated) {
                    $user = User::create([
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                        'password' => Hash::make($validated['password']),
                        'role' => $validated['type'],
                    ]);

                    Mentor::create([
                        'description' => $validated['description'],
                        'expertise' => $validated['expertise'],
                        'experience' => $validated['experience'],
                        'user_id' => $user->id,
                    ]);
                });
                break;

            case 'web_editor':
            case 'committee_member':
                User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => $validated['type'],
                ]);
                break;
        }

        return response()->json(['message' => 'Účet úspešne vytvorený.'], Response::HTTP_CREATED);
    }

    public function deleteUserAccount(User $user, FileService $fileService) {
        if($user->role == 'student') {
            $student = $user->student;
            $fileService->deleteFile($student->curriculumVitae);
            $student->delete();
        }
        else if($user->role == 'company_admin') {
            $company = $user->company;
            $fileService->deleteFile($company->logo);
            $company->delete();
        }
        else if($user->role == 'company_member') {
            $company = $user->company;
            $company->company_employees()->detach($user->id);
        }
        else if($user->role == 'mentor') {
            $mentor = $user->mentor;
            $mentor->delete();
        }
        $user->delete();
        return response()->json(['message' => 'Účet zmazaný.'], Response::HTTP_OK);
    }
}
