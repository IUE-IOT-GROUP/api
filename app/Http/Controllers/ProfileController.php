<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        return new UserResource($request->user());
    }
/*
    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required'],
            'email' => ['required'],
            'phone_number' => ['nullable'],
            'password' => ['nullable', 'confirmed', Password::min(4)->numbers()]
        ]);

        $request->user()->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);
    }

    public function destroy($id)
    {
        //
    }*/
}
