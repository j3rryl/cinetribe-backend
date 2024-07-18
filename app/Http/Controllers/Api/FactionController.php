<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class FactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        //
        $search = $request->query('query', '');
        $query = Faction::query();
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }
        $factions = $query->paginate(10);
        return response()->json($factions);
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
            'media_id' => 'nullable|exists:genres,id',
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
            $faction = Faction::create($validator->validated());
            
            if ($request->hasFile('thumbnail')) {
                
                $thumbnailPath = $request->file('thumbnail')->store('faction_thumbnails');
                $faction->thumbnail = $thumbnailPath;
                $faction->save();
            } 
            return response()->json([
                'message' => 'Faction created successfully',
                'faction' => $faction,
            ], 201); 
        } catch (\Exception $e) {
            info($e);
            return response()->json(['message' => 'Failed to create faction'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        //
        $faction = Faction::findOrFail($id); 
        return response()->json($faction);
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
            'media_id' => 'sometimes|nullable|exists:genres,id',
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
            $faction = Faction::findOrFail($id);
            $validatedData = $validator->validated();
            if ($request->hasFile('thumbnail')) {
                if ($faction->thumbnail) {
                    Storage::delete($faction->thumbnail);
                }
                $thumbnailPath = $request->file('thumbnail')->store('faction_thumbnails');
                $validatedData['thumbnail'] = $thumbnailPath;
            } 
            $faction->update($validatedData);

            return response()->json([
                'message' => 'Faction updated successfully',
                'faction' => $faction->toArray(),
            ], 200);
        } catch (\Exception $e) {
            info($e->getMessage());
            return response()->json(['message' => 'Failed to update faction'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        //
        try {
            $faction = Faction::with("images")->findOrFail($id); 
            if ($faction->thumbnail) {
                Storage::delete($faction->thumbnail);
            }

            foreach ($faction->images as $image) {
                Storage::delete($image->thumbnail);
                $image->delete();
            }
            
            $faction->delete(); //Delete the images also
            return response()->json(['message' => 'Faction deleted successfully']);
        } catch (\Exception $e) {
            info($e);
            return response()->json(['message' => 'Failed to delete faction'], 500);
        }
    }
}
