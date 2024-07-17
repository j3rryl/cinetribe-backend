<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('query', '');
        $query = User::query();
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        $users = $query->paginate(10);
        return response()->json($users);
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
            'email' => 'required|string',
            'phone_number' => 'sometimes|string',
            'status' => 'required|string',
            'gender' => 'required|string',
            'dob' => 'required|string',
            'password' => 'required|string',
            'thumbnail' => 'sometimes|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422); 
        }
    
        try {
            $validatedData = $validator->validated();
            $validatedData["password"] = bcrypt($validatedData["password"]);
            $user = User::create($validatedData);
            
            if ($request->hasFile('thumbnail')) {
                
                $thumbnailPath = $request->file('thumbnail')->store('user_thumbnails');
                $user->thumbnail = $thumbnailPath;
                $user->save();
            } 
            return response()->json([
                'message' => 'User created successfully',
                'user' => $user,
            ], 201); 
        } catch (\Exception $e) {
            info($e);
            return response()->json(['message' => 'Failed to create user'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        //
        $user = User::findOrFail($id); 
        return response()->json($user);
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
            'phone_number' => 'sometimes|required|string',
            'email' => 'sometimes|required|string',
            'status' => 'sometimes|required|string',
            'gender' => 'sometimes|required|string',
            'dob' => 'sometimes|required|string',
            'thumbnail' => 'sometimes|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422); 
        }
    
        try {
            $user = User::findOrFail($id);
            $validatedData = $validator->validated();
            if ($request->hasFile('thumbnail')) {
                if ($user->thumbnail) {
                    Storage::delete($user->thumbnail);
                }
                $thumbnailPath = $request->file('thumbnail')->store('user_thumbnails');
                $validatedData['thumbnail'] = $thumbnailPath;
            } 
            $user->update($validatedData);

            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user->toArray(),
            ], 200);
        } catch (\Exception $e) {
            info($e->getMessage());
            return response()->json(['message' => 'Failed to update user'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        //
        try {
            $user = User::findOrFail($id); 
            if ($user->thumbnail) {
                Storage::delete($user->thumbnail);
            }
            $user->delete(); 
            return response()->json(['message' => 'User deleted successfully']);
        } catch (\Exception $e) {
            info($e);
            return response()->json(['message' => 'Failed to delete user'], 500);
        }
    }
}
