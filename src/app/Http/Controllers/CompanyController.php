<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Resources\CompanyResource;
use Symfony\Component\HttpFoundation\Response;

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
}