<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\StoreRequest;
use App\Models\User;
use Exception;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function login(StoreRequest $request): \Illuminate\Http\JsonResponse
    {

        try
        {
            $user = User::where('email', $request->email)->firstOrFail();
        } catch (Exception $e)
        {
            throw ValidationException::withMessages([
                'email' => 'The given email does not exists.',
            ]);
        }

        if (!\Hash::check($request->password, $user->password))
        {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials are incorrect.',
            ]);
        }

        return response()->json([
            'id' => $user->id,
            'username' => $user->name,
            'email' => $user->email,
            'token' => $user->createToken($request->device_name)->plainTextToken,
        ]);
    }
}
