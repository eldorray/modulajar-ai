<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        $query->when($request->filled('search'), fn ($q) => $q->where(fn ($nested) => $nested
            ->where('name', 'like', '%'.$request->string('search').'%')
            ->orWhere('email', 'like', '%'.$request->string('search').'%')));
        $query->when($request->filled('role'), fn ($q) => $q->where('role', $request->string('role')));
        $p = $query->latest()->paginate(15);

        return api_response()->paginated($p, UserResource::collection($p));
    }

    public function store(Request $r)
    {
        $d = $r->validate($this->rules());
        $d['password'] = Hash::make($d['password']);

        return api_response()->success(UserResource::make(User::create($d)), 201);
    }

    public function show(User $user)
    {
        return api_response()->success(UserResource::make($user));
    }

    public function update(Request $r, User $user)
    {
        $d = $r->validate($this->rules($user));
        if (isset($d['password'])) {
            $d['password'] = Hash::make($d['password']);
        } else {
            unset($d['password']);
        }$user->update($d);

        return api_response()->success(UserResource::make($user->fresh()));
    }

    public function destroy(Request $r, User $user): Response
    {
        if ($r->user()->is($user)) {
            abort(403, 'Tidak dapat menghapus akun sendiri.');
        }$user->delete();

        return response()->noContent();
    }

    private function rules(?User $u = null): array
    {
        return ['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($u?->id)], 'password' => [$u ? 'nullable' : 'required', 'confirmed', Password::defaults()], 'role' => ['required', Rule::in(['admin', 'guru'])]];
    }
}
