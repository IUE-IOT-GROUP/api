<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:4'
        ]);

        $user = new User;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = \Hash::make($request->input('password'));
        $user->save();

        return response()->json([
            'name' => $user->name,
            'email' => $user->email
        ]);
    }

    public function storeFromFog(Request $request)
    {
        $data = $request->only(User::FIELDS);


        User::withoutEvents(function() use ($data) {
            User::create($data);
        });
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function update(Request $request, User $user)
    {
        $fields = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'sometimes|min:4',
            'phone_number' => 'nullable'
        ]);

        $user->update($fields);

        return $this->success();
    }

    public function destroy(User $user)
    {

    }
}
