<?php

namespace App\Http\Controllers;

use App\Cloud\Cloud;
use App\Http\Requests\Auth\StoreRequest;
use App\Models\User;
use Exception;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function login(StoreRequest $request)
    {
        if (isFog())
        {
            return $this->cloudLogin([
                'email' => $request->get('email'),
                'password' => $request->get('password'),
                'device_name' => $request->get('device_name'),
            ]);
        }

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

    private function cloudLogin($request)
    {
        $response = Cloud::post('login', $request);

        $escaped = preg_quote('='.config('ims.auth_token'), '/');
        $escaped = "/^IMS_AUTH_TOKEN{$escaped}/m";

        [$id, $token] = explode('|', $response['token']);

        $user = User::whereEmail($request['email'])->first();
        $localToken = $user->tokens()->create([
            'token' => hash('sha256', $token),
            'name' => $request['device_name'],
            'abilities' => ['*']
        ]);

        ray($token);

        $response['token'] = $localToken->id.'|'.$token;
        ray($response);

        file_put_contents(base_path('.env'), preg_replace(
            $escaped,
            'IMS_AUTH_TOKEN=' . $response['token'],
            file_get_contents(base_path('.env'))
        ));

        return $response;
    }
}
