<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\File;
use App\Models\Student;
use App\Models\User;
use App\Services\FileService;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
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
        elseif ($user->role === 'company_member') {
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
}
