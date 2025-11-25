<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        
        return response()->json([
            'data' => $user,
            'success' => true
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user->update($request->only([
            'first_name', 
            'last_name', 
            'username', 
            'email', 
            'phone', 
            'address'
        ]));

        return response()->json([
            'data' => $user,
            'message' => 'Profile updated successfully',
            'success' => true
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect',
                'success' => false
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Password updated successfully',
            'success' => true
        ]);
    }
}