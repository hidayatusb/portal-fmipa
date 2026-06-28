<?php

namespace App\Http\Controllers\Api;

use App\Support\FcmTopic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FcmTopicController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $topics = FcmTopic::forUser($request->user());

        return $this->success([
            'topics' => $topics,
            'subscribe_hint' => [
                'students' => 'course_{course_id}_students',
                'lecturer' => 'course_{course_id}_lecturer',
            ],
        ]);
    }
}
