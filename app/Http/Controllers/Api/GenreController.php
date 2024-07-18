<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Genre;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        //
        $search = $request->query('query', '');
        $query = Genre::query();
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }
        $genres = $query->paginate(10);
        return response()->json($genres);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'thumbnail' => 'sometimes|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422); 
        }
    
        try {
            $genre = Genre::create($validator->validated());
            
            if ($request->hasFile('thumbnail')) {
                
                $thumbnailPath = $request->file('thumbnail')->store('public/genre_thumbnails');
                $genre->thumbnail = $thumbnailPath;
                $genre->save();
            } 
            return response()->json([
                'message' => 'Genre created successfully',
                'genre' => $genre,
            ], 201); 
        } catch (\Exception $e) {
            info($e);
            return response()->json(['message' => 'Failed to create genre'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        //
        $genre = Genre::findOrFail($id); 
        return response()->json($genre);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        //
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'thumbnail' => 'sometimes|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422); 
        }
    
        try {
            $genre = Genre::findOrFail($id);
            $validatedData = $validator->validated();
            if ($request->hasFile('thumbnail')) {
                if ($genre->thumbnail) {
                    Storage::delete($genre->thumbnail);
                }
                $thumbnailPath = $request->file('thumbnail')->store('public/genre_thumbnails');
                $validatedData['thumbnail'] = $thumbnailPath;
            } 
            $genre->update($validatedData);

            return response()->json([
                'message' => 'Genre updated successfully',
                'genre' => $genre->toArray(),
            ], 200);
        } catch (\Exception $e) {
            info($e->getMessage());
            return response()->json(['message' => 'Failed to update genre'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        //
        try {
            $genre = Genre::findOrFail($id); 
            if ($genre->thumbnail) {
                Storage::delete($genre->thumbnail);
            }
            $genre->delete(); 
            return response()->json(['message' => 'Genre deleted successfully']);
        } catch (\Exception $e) {
            info($e);
            return response()->json(['message' => 'Failed to delete genre'], 500);
        }
    }
}
