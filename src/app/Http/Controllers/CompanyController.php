<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Resources\CompanyResource;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Models\Challenge;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::all();
        return CompanyResource::collection($companies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getAllLogos()
    {
        $logos = Company::select('company_name', 'logo_id')
            ->get()
            ->map(function ($company) {
                return [
                    'name' => $company->company_name,
                    'logo' => $company->logo->url,
                ];
            });

        return response()->json($logos, Response::HTTP_OK);
    }

    public function companyMembersInfo() {
        $company = auth()->user()->company;
        $members = $company->company_employees()->get()->map(function ($member) {
            return [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
            ];
        });
        return response()->json($members, Response::HTTP_OK);
    }

    public function storeCompanyMember(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        $member = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'company_member',
            'email_verified_at' => now(),
            'link_for_password_reset' => null,
            'expiration_of_link_for_password_reset' => null
        ]);
        $company = auth()->user()->company;
        $company->company_employees()->attach($member->id);

        return response()->json([
            'message' => 'Company member created successfully',
        ], Response::HTTP_CREATED);
    }

    public function deleteCompanyMember(User $member) {
        $company = auth()->user()->company;
        $company->company_employees()->detach($member->id);
        $member->delete();
        return response()->json([
            'message' => 'Company member deleted successfully',
        ], Response::HTTP_OK);
    }

    public function getAllChallenges() {
        $user = auth()->user();
        if($user->role === 'company_admin') {
            $challenges = Challenge::where('user_id', $user->id)
                ->whereNot('status', 'proposed')
                ->get();
        }
        else if($user->role === 'company_member') {
            $company = auth()->user()->company;
            $company_admin = $company->user;
            $challenges = Challenge::where('user_id', $company_admin->id)
                ->whereNot('status', 'proposed')
                ->where('product_owner_id', $user->id)
                ->get();
        }
        
        $response = $challenges->map(function ($challenge) {
            $data = [
                'id' => $challenge->id,
                'status' => $challenge->status,
                'name_of_project' => $challenge->name,
                'name_of_product_owner' => $challenge->product_owner->name,
                'documentation' => $challenge->proposal_file->url,
                'project_description' => $challenge->description,
                'reward' => $challenge->reward,
            ];

            if($challenge->status === 'in_progress' || $challenge->status=== 'finished') {
                $data['name_of_team'] = $challenge->attached_team->name;
                $data['team_members'] = $challenge->attached_team->teamMembers->map(function ($teamMember) {
                    return [
                        'name' => $teamMember->user->name,
                        'email' => $teamMember->user->email,
                        'status' => $teamMember->pivot->status,
                    ];
                });
            }

            if($challenge->status=== 'finished') {
                $data['final_assessment'] = $challenge->final_assessment;
            }

            return $data;
        });

        

        return response()->json($response, Response::HTTP_OK);
    }

    public function getRegistrationRequests() {
        $companies = Company::where('is_approved_by_admin', false)
            ->get()
            ->map(function ($company) {
                return [
                    'id' => $company->id,
                    'name_of_company' => $company->user->name,
                    'ico' => $company->ico,
                    'dic' => $company->dic,
                    'name_of_contact_person' => $company->name_of_contact_person,
                    'email' => $company->user->email,
                    'logo' => $company->logo->url,
                ];
            });
        
        return response()->json([
            'companies' => $companies
        ], Response::HTTP_OK);
    }

    public function approveRegistration(Company $company) {
        if($company->is_approved_by_admin) {
            return response()->json([
                'message' => 'Registration request already approved'
            ], Response::HTTP_OK);
        }

        $company->is_approved_by_admin = true;
        $company->save();
        return response()->json([
            'message' => 'Registration request approved'
        ], Response::HTTP_OK);
    }

    public function rejectRegistration(Company $company, FileService $fileService) {
        if($company->is_approved_by_admin) {
            return response()->json([
                'message' => 'Registration request already approved'
            ], Response::HTTP_OK);
        }
        
        try {
            DB::transaction(function () use ($company, $fileService) {
                $logo = $company->logo;
                $company->delete();
                $fileService->deleteFile($logo);
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