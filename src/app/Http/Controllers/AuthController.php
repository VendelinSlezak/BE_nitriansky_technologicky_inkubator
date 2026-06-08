<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Events\PasswordReset;

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
            'name' => $user->name,
            'role' => $user->role,
            'dashboard' => $user->getDashboardUrl(),
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

    public function resetPasswordRequest(Request $request) {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            // 'g-recaptcha-response' => ['required', new Recaptcha], // úmyselne zakomentované aby sa nestalo že systém nás pri prezentácií nepustí dovnútra kvôli captcha
        ]);

        $user = User::where('email', $validated['email'])->first();
        $user->update([
            'token_for_password_reset' => bin2hex(random_bytes(32)),
            'expiration_of_token_for_password_reset' => now()->addMinutes(15),
        ]);

        event(new PasswordReset($user));

        return response()->json([
            'message' => 'Password reset link was sent to your email.',
        ], Response::HTTP_OK);
    }

    public function resetPassword(Request $request) {
        $validated = $request->validate([
            'token' => ['required', 'string', 'exists:users,token_for_password_reset'],
            'password' => ['required', 'confirmed'],
        ]);
        $user = User::where('token_for_password_reset', $validated['token'])->first();
        if($user->expiration_of_token_for_password_reset < now()) {
            return response()->json([
                'message' => 'Password reset link has expired.',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $user->update([
            'password' => Hash::make($validated['password']),
            'token_for_password_reset' => null,
            'expiration_of_token_for_password_reset' => null,
        ]);
        return response()->json([
            'message' => 'Password was changed.',
        ], Response::HTTP_OK);
    }
}