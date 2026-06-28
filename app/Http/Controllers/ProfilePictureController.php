<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfilePictureController extends Controller
{
    public function show(): StreamedResponse
    {
        $user = Auth::user();

        abort_unless($user && $user->hasProfilePicture(), 404);

        return Storage::disk('public')->response(
            $user->profile_picture,
            'profile-'.$user->id.'.'.pathinfo($user->profile_picture, PATHINFO_EXTENSION),
            ['Content-Disposition' => 'inline']
        );
    }
}
