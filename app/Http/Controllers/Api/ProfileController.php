<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfileController extends ApiController
{
    public function show(Request $request): JsonResponse
    {
        return $this->success(UserResource::make($request->user()));
    }

    public function picture(Request $request): StreamedResponse
    {
        $user = $request->user();

        abort_unless($user->hasProfilePicture(), 404);

        return Storage::disk('public')->response(
            $user->profile_picture,
            'profile-'.$user->id.'.'.pathinfo($user->profile_picture, PATHINFO_EXTENSION),
            ['Content-Disposition' => 'inline']
        );
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'min:3', 'max:150'],
            'username' => ['sometimes', 'required', 'string', 'min:3', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['sometimes', 'required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => ['required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
            'remove_profile_picture' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('remove_profile_picture') && $user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
            $user->profile_picture = null;
        }

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $user->profile_picture = $request->file('profile_picture')->store('profile-pictures', 'public');
        }

        $user->fill(collect($validated)->only(['name', 'username', 'email'])->filter()->all());

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return $this->success(UserResource::make($user->fresh()), 'Profil berhasil diperbarui.');
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ], [
            'password.required' => 'Password wajib diisi untuk menghapus akun.',
            'password.current_password' => 'Password tidak sesuai.',
        ]);

        $user = $request->user();
        $user->deleteAccount();

        return $this->success(null, 'Akun berhasil dihapus.');
    }
}
