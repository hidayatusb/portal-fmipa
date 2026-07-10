<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\AnnouncementContentType;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AnnouncementController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $announcements = Announcement::query()
            ->with('author')
            ->when($request->filled('is_published'), function ($query) use ($request) {
                $query->where('is_published', $request->boolean('is_published'));
            })
            ->when($request->filled('content_type'), function ($query) use ($request) {
                $query->where('content_type', $request->string('content_type'));
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = '%'.$request->string('search').'%';

                $query->where(function ($inner) use ($term) {
                    $inner->where('title', 'like', $term)
                        ->orWhere('body', 'like', $term);
                });
            })
            ->latest('published_at')
            ->latest('id')
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
        $announcement->loadMissing('author');

        return $this->success(AnnouncementResource::make($announcement));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validatePayload($request, requireImage: true);

        $isPublished = $request->boolean('is_published', true);

        $announcement = Announcement::query()->create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'content_type' => $validated['content_type'],
            'body' => $validated['body'],
            'image_path' => $request->file('image')->store('announcements', 'public'),
            'is_published' => $isPublished,
            'published_at' => $isPublished ? now() : null,
        ]);

        $announcement->load('author');

        return $this->success(
            AnnouncementResource::make($announcement),
            'Pengumuman berhasil ditambahkan.',
            201
        );
    }

    public function update(Request $request, Announcement $announcement): JsonResponse
    {
        $validated = $this->validatePayload($request, requireImage: ! $announcement->hasImage(), sometimes: true);

        if (array_key_exists('title', $validated)) {
            $announcement->title = $validated['title'];
        }

        if (array_key_exists('content_type', $validated)) {
            $announcement->content_type = $validated['content_type'];
        }

        if (array_key_exists('body', $validated)) {
            $announcement->body = $validated['body'];
        }

        if ($request->has('is_published')) {
            $isPublished = $request->boolean('is_published');
            $announcement->is_published = $isPublished;

            if ($isPublished && ! $announcement->published_at) {
                $announcement->published_at = now();
            }
        }

        if ($request->hasFile('image')) {
            if ($announcement->image_path) {
                Storage::disk('public')->delete($announcement->image_path);
            }

            $announcement->image_path = $request->file('image')->store('announcements', 'public');
        }

        $announcement->save();
        $announcement->load('author');

        return $this->success(
            AnnouncementResource::make($announcement),
            'Pengumuman berhasil diperbarui.'
        );
    }

    public function destroy(Announcement $announcement): JsonResponse
    {
        Announcement::destroy($announcement->id);

        return $this->success(null, 'Pengumuman berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function validatePayload(Request $request, bool $requireImage, bool $sometimes = false): array
    {
        $contentType = $request->input('content_type', AnnouncementContentType::Text->value);

        $bodyRules = $contentType === AnnouncementContentType::Url->value
            ? ['required', 'url', 'max:2000']
            : ['required', 'string', 'min:3'];

        if ($sometimes) {
            $bodyRules = array_merge(['sometimes'], $bodyRules);
        }

        return $request->validate([
            'title' => array_values(array_filter([
                $sometimes ? 'sometimes' : null,
                'required',
                'string',
                'min:3',
                'max:200',
            ])),
            'content_type' => array_values(array_filter([
                $sometimes ? 'sometimes' : null,
                'required',
                Rule::enum(AnnouncementContentType::class),
            ])),
            'body' => $bodyRules,
            'image' => [$requireImage ? 'required' : 'nullable', 'image', 'max:4096'],
            'is_published' => ['nullable', 'boolean'],
        ]);
    }
}
