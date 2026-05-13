<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Resources\CompanyResource;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

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
}