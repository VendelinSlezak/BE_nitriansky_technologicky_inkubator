<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller {
    public function login(Request $request) {
        $validated = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();
        if(!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Nesprávny email alebo heslo.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if($user->isStudent() && $user->student->is_accepted_by_admin == false) {
            return response()->json([
                'message' => 'Používateľ nie je potvrdený adminom.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('auth-token')->plainTextToken;
        return response()->json([
            'token' => $token,
        ], Response::HTTP_OK);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Successfully logged out'
        ], Response::HTTP_OK);
    }

    public function logoutAll(Request $request) {
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'Successfully logged out from all devices'
        ], Response::HTTP_OK);
    }
}
