<?php

namespace App\Notifications\Channels;

use App\Services\FcmService;
use Illuminate\Notifications\Notification;

class FcmChannel
{
    public function __construct(protected FcmService $fcm) {}

    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'pushPayload')) {
            return;
        }

        $tokens = $notifiable->deviceTokens()->pluck('token')->all();

        if ($tokens === []) {
            return;
        }

        /** @var array{title: string, body?: string, data?: array<string, string|int|bool|null>} $payload */
        $payload = $notification->pushPayload($notifiable);

        $this->fcm->sendToTokens($tokens, $payload);
    }
}
