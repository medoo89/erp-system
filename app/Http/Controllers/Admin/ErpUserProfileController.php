<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ErpUserProfileController extends Controller
{
    public function edit()
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        return view('admin.profile.edit', [
            'user' => auth()->user(),
        ]);
    }

    public function update(Request $request)
    {
        abort_unless(auth()->check() && (auth()->user()->is_admin ?? false), 403);

        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:15360'],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $payload = [
            'name' => $validated['name'],
        ];

        if (Schema::hasColumn('users', 'phone')) {
            $payload['phone'] = $validated['phone'] ?? null;
        }

        if ($request->hasFile('avatar') && Schema::hasColumn('users', 'avatar_path')) {
            if (! empty($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $payload['avatar_path'] = $request->file('avatar')->store('user-avatars', 'public');
        }

        if (! empty($validated['new_password'])) {
            $payload['password'] = Hash::make($validated['new_password']);
        }

        $user->forceFill($payload)->save();

        return redirect()
            ->route('admin.my-profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}
