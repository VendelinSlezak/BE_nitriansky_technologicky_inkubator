<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FaqQuestion;
use Illuminate\Http\Response;
use App\Http\Resources\FaqQuestionResource;

class FaqQuestionController extends Controller
{
    public function getFaqFromProgramA() {
        $questions = FaqQuestion::where('type', 'A')->get();
        return FaqQuestionResource::collection($questions);
    }

    public function getFaqFromProgramB() {
        $questions = FaqQuestion::where('type', 'B')->get();
        return FaqQuestionResource::collection($questions);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'type' => 'required|string|in:A,B',
            'question' => 'required|string',
            'answer' => 'required|string',
        ]);

        $faqQuestion = FaqQuestion::create($validated);

        return response()->json([
            'message' => 'FAQ question created successfully',
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, FaqQuestion $faqQuestion) {
        $validated = $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
        ]);

        $faqQuestion->update($validated);

        return response()->json([
            'message' => 'FAQ question updated successfully',
        ], Response::HTTP_OK);
    }

    public function destroy(FaqQuestion $faqQuestion) {
        $faqQuestion->delete();

        return response()->json([
            'message' => 'FAQ question deleted successfully',
        ], Response::HTTP_OK);
    }
}
