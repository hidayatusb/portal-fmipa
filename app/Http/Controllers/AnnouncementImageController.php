<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnnouncementImageController extends Controller
{
    public function show(Announcement $announcement): StreamedResponse
    {
        abort_unless($announcement->hasImage(), 404);

        if (! $announcement->is_published) {
            abort_unless(auth()->user()?->isAdmin(), 404);
        }

        return Storage::disk('public')->response(
            $announcement->image_path,
            'pengumuman-'.$announcement->id.'.'.pathinfo($announcement->image_path, PATHINFO_EXTENSION),
            ['Content-Disposition' => 'inline']
        );
    }
}
