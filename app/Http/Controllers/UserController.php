<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json($user, 200);

    }

    public function update(Request $request)
    {

        $user = $request->user();

        $validatedData = $request->validate([
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'business_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        $user->update($validatedData);

        return response()->json($user, 200);

    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'current_password' => ['required', 'string', 'current_password:api'],
            'password' => ['required', 'confirmed', Password::defaults()],

        ]);

        $user->update([
            'password' => $validatedData['password'],
        ]);

        $user->tokens()->delete();

        return response()->json(['message' => 'Password updated successfully'], 200);

    }

    //TODO: delete method
    // public function destroy(Request $request)
    // {

    // }
}

