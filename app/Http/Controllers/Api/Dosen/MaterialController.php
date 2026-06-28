<?php

namespace App\Http\Controllers\Api\Dosen;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\MaterialResource;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Support\CourseStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MaterialController extends ApiController
{
    public function store(Request $request, Course $course): JsonResponse
    {
        $this->authorizeCourse($course);

        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:150'],
            'type' => ['required', 'in:document,video,link,text'],
            'content' => ['nullable', 'string', 'max:5000'],
            'file' => ['nullable', 'file', 'max:10240'],
        ]);

        $filePath = null;
        $fileName = null;

        if ($request->hasFile('file')) {
            $fileName = $request->file('file')->getClientOriginalName();
            $filePath = $request->file('file')->store(CourseStorage::materialsDirectory($course), CourseStorage::diskName());
        }

        $material = $course->materials()->create([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'content' => $validated['content'] ?? null,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'sort_order' => ($course->materials()->max('sort_order') ?? 0) + 1,
        ]);

        return $this->success(MaterialResource::make($material), 'Materi berhasil ditambahkan.', 201);
    }

    public function destroy(Course $course, CourseMaterial $material): JsonResponse
    {
        $this->authorizeCourse($course);
        abort_unless($material->course_id === $course->id, 404);

        $material->delete();

        return $this->success(message: 'Materi berhasil dihapus.');
    }

    public function file(Course $course, CourseMaterial $material): StreamedResponse|JsonResponse
    {
        $this->authorizeCourse($course);
        abort_unless($material->course_id === $course->id, 404);
        abort_unless($material->hasFile(), 404);

        return CourseStorage::diskForPath($material->file_path)->download($material->file_path, $material->file_name);
    }

    protected function authorizeCourse(Course $course): void
    {
        abort_unless($course->user_id === Auth::id(), 403);
    }
}
