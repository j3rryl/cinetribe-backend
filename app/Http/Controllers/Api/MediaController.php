<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Media;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        //
        $search = $request->query('query', '');
        $query = Media::query();
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }
        $media = $query->with(['genre' => function($query) {
            $query->select('id', 'name');
        }])->paginate(10);
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
    public function store(Request $request): JsonResponse
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'genre_id' => 'nullable|exists:genres,id',
            'description' => 'required|string',
            'thumbnail' => 'sometimes|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
            
            if ($request->hasFile('thumbnail')) {
                
                $thumbnailPath = $request->file('thumbnail')->store('public/media_thumbnails');
                $media->thumbnail = $thumbnailPath;
                $media->save();
            } 
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
    public function show(string $id): JsonResponse
    {
        //
        $media = Media::with(['genre' => function($query) {
            $query->select('id', 'name'); 
        }])->findOrFail($id); 
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
    public function update(Request $request, string $id): JsonResponse
    {
        //
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string',
            'genre_id' => 'sometimes|nullable|exists:genres,id',
            'description' => 'sometimes|required|string',
            'thumbnail' => 'sometimes|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
            $validatedData = $validator->validated();
            if ($request->hasFile('thumbnail')) {
                if ($media->thumbnail) {
                    Storage::delete($media->thumbnail);
                }
                $thumbnailPath = $request->file('thumbnail')->store('public/media_thumbnails');
                $validatedData['thumbnail'] = $thumbnailPath;
            } 
            $media->update($validatedData);

            return response()->json([
                'message' => 'Media updated successfully',
                'media' => $media->toArray(),
            ], 200);
        } catch (\Exception $e) {
            info($e->getMessage());
            return response()->json(['message' => 'Failed to update media'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        //
        try {
            $media = Media::with("images")->findOrFail($id); 
            if ($media->thumbnail) {
                Storage::delete($media->thumbnail);
            }

            foreach ($media->images as $image) {
                Storage::delete($image->thumbnail);
                $image->delete();
            }
            
            $media->delete(); //Delete the images also
            return response()->json(['message' => 'Media deleted successfully']);
        } catch (\Exception $e) {
            info($e);
            return response()->json(['message' => 'Failed to delete media'], 500);
        }
    }
}
