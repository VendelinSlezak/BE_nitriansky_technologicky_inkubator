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
}
