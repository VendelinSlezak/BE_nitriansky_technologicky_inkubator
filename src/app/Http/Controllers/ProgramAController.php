<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProgramAResource;
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
        $categories = ProgramACategory::all();
        return response()->json([
            'categories' => ProgramAResource::collection($categories)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'skills_description' => 'required|string',
        ]);
        $category = ProgramACategory::create([
            'title' => $validated['title'],
            'description_of_skills' => $validated['skills_description'],
            'status' => "visible"
        ]);

        return response()->json(['id' => $category->id],Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'skills_description' => 'required|string',
            'status' => 'required|string|in:visible,invisible',
        ]);
        $category = ProgramACategory::find($id);

        if (!$category) {
            return response()->json(['error' => 'Category Not Found'], Response::HTTP_NOT_FOUND);
        }
        $category->update([
            'title' => $validated['title'],
            'description_of_skills' => $validated['skills_description'],
            'status' => $validated['status'],
        ]);

        return response()->json($category,Response::HTTP_OK);

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
