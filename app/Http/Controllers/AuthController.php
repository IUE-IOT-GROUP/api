<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
            'device_name' => 'required'
        ]);

        try {
            $user = User::where('email', $request->email)->firstOrFail();
        } catch (Exception $e) {
            throw ValidationException::withMessages([
                'email' => 'The given email does not exists.'
            ]);
        }

        if (!\Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials are incorrect.'
            ]);
        }

        return response()->json([
            'username' => $user->name,
            'email' => $user->email,
            'token' => $user->createToken($request->device_name)->plainTextToken
        ]);
    }
}
