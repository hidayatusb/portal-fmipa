<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnnouncementController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $announcements = Announcement::query()
            ->published()
            ->with('author')
            ->latest('published_at')
            ->paginate($request->integer('per_page', 20));

        return $this->success([
            'items' => AnnouncementResource::collection($announcements),
            'meta' => [
                'current_page' => $announcements->currentPage(),
                'last_page' => $announcements->lastPage(),
                'per_page' => $announcements->perPage(),
                'total' => $announcements->total(),
            ],
        ]);
    }

    public function show(Announcement $announcement): JsonResponse
    {
        abort_unless($announcement->is_published && $announcement->published_at?->lte(now()), 404);

        $announcement->loadMissing('author');

        return $this->success(AnnouncementResource::make($announcement));
    }

    public function image(Announcement $announcement): StreamedResponse
    {
        abort_unless($announcement->hasImage(), 404);

        if (! $announcement->is_published || $announcement->published_at?->gt(now())) {
            abort_unless(auth()->user()?->isAdmin(), 404);
        }

        return Storage::disk('public')->response(
            $announcement->image_path,
            'pengumuman-'.$announcement->id.'.'.pathinfo($announcement->image_path, PATHINFO_EXTENSION),
            ['Content-Disposition' => 'inline']
        );
    }
}
