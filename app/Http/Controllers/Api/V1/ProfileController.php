<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    public function show(Request $r)
    {
        return api_response()->success(UserResource::make($r->user()));
    }

    public function update(Request $r)
    {
        $d = $r->validate(['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($r->user()->id)]]);
        $emailChanged = $d['email'] !== $r->user()->email;
        $r->user()->fill($d);
        if ($emailChanged) {
            $r->user()->email_verified_at = null;
        }
        $r->user()->save();

        return api_response()->success(UserResource::make($r->user()->fresh()));
    }

    public function password(Request $r): Response
    {
        $d = $r->validate(['current_password' => ['required', 'current_password'], 'password' => ['required', 'confirmed', Password::defaults()]]);
        $r->user()->update(['password' => Hash::make($d['password'])]);

        return response()->noContent();
    }

    public function destroy(Request $r): Response
    {
        $r->validate(['password' => ['required', 'current_password']]);
        $u = $r->user();
        $u->tokens()->delete();
        $u->delete();

        return response()->noContent();
    }
}
