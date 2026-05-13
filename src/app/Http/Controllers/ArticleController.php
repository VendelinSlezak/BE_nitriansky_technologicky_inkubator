<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Http\Response;
use App\Http\Resources\ArticleResource;
use App\Services\FileService;
use Throwable;
use App\Models\File;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::all();
        return ArticleResource::collection($articles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, FileService $fileService)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'perex' => 'required|string',
            'content' => 'required|string',
            'image' => 'required|file|max:2048',
            'image_description' => 'required|string',
        ]);

        try {
            $fileService->uploadAndCreateRecord(
                $request->file('image'), 
                'articles',
                'public',
                function (File $fileRecord) use ($validated) {
                    $article = Article::create([
                        'title' => $validated['title'],
                        'perex' => $validated['perex'],
                        'content' => $validated['content'],
                        'image_id' => $fileRecord->id,
                        'image_description' => $validated['image_description'],
                        'published_at' => now(),
                        'user_id' => auth()->user()->id,
                    ]);
                }
            );

            return response()->json([
                'message' => 'Článok bol pridaný.'
            ], Response::HTTP_CREATED);
        }
        catch(Throwable $e) {
            return response()->json([
                'message' => 'Pridanie článku zlyhala. Skúste to neskôr.',
                'error' => config('app.debug') ? $e->getMessage() : null // Debug info len pre vývoj
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $article = Article::findOrFail($id);
        return new ArticleResource($article);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article, FileService $fileService)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'perex' => 'sometimes|string',
            'content' => 'sometimes|string',
            'image' => 'sometimes|file|max:2048',
            'image_description' => 'sometimes|string',
        ]);

        try {
            if ($request->hasFile('image')) {
                $fileService->uploadAndCreateRecord(
                    $request->file('image'),
                    'articles',
                    'public',
                    function (File $fileRecord) use (&$validated, $article, $fileService) {
                        $oldImage = $article->image;
                        $validated['image_id'] = $fileRecord->id;
                        $article->update($validated);
                        $fileService->deleteFile($oldImage);
                    }
                );
            }
            else {
                $article->update($validated);
            }

            return response()->json(['message' => 'Článok bol úspešne aktualizovaný.']);
            
        }
        catch(\Throwable $e) {
            return response()->json([
                'message' => 'Aktualizácia zlyhala.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article, FileService $fileService)
    {
        try {
            DB::transaction(function () use ($article, $fileService) {
                $image = $article->image;
                $article->delete();
                $fileService->deleteFile($image);
            });

            return response()->json([
                'message' => 'Článok bol úspešne zmazaný.'
            ], Response::HTTP_OK);
        }
        catch(\Throwable $e) {
            return response()->json([
                'message' => 'Zmazanie zlyhalo.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
