<?php

namespace App\Http\Controllers;

use App\Models\ProgramACategory;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class ProgramAController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $category = ProgramACategory::create([
            'title' => $request->title,
            'description_of_skills' => $request->skillsDescription,
            'statutory_declaration_id' => $request->statutory_declaration_id,
        ]);

        return response()->json(['id' => $category->id],Response::HTTP_OK);
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
        $category = ProgramACategory::find($id);
        if (!$category) {
            return response()->json(['error' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }
        $category->delete();
        return response()->json('Category deleted successfully', Response::HTTP_OK);
    }
}
