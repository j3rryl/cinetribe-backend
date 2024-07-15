<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Media;
use Illuminate\Support\Facades\Validator;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $media = Media::paginate(10); 
        return response()->json($media);
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
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'genre_id' => 'nullable|exists:genres,id',
            'description' => 'required|string',
            'thumbnail' => 'nullable|string',
            'media_type' => 'required|in:movie,music,sport',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422); 
        }
    
        try {
            $media = Media::create($validator->validated());
            return response()->json([
                'message' => 'Media created successfully',
                'media' => $media,
            ], 201); 
        } catch (\Exception $e) {
            info($e);
            return response()->json(['message' => 'Failed to create media'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $media = Media::findOrFail($id); 
        return response()->json($media);
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
    public function update(Request $request, string $id)
    {
        //
        info($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string',
            'genre_id' => 'sometimes|nullable|exists:genres,id',
            'description' => 'sometimes|required|string',
            'thumbnail' => 'sometimes|nullable|string',
            'media_type' => 'sometimes|required|in:movie,music,sport',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422); 
        }
    
        try {
            $media = Media::findOrFail($id);
            $media->update($validator->validated());
    
            return response()->json([
                'message' => 'Media updated successfully',
                'media' => $media,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update media'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try {
            $media = Media::findOrFail($id); 
            $media->delete(); 
            return response()->json(['message' => 'Media deleted successfully']);
        } catch (\Exception $e) {
            info($e);
            return response()->json(['message' => 'Failed to delete media'], 500);
        }
    }
}
