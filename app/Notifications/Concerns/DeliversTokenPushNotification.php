<?php

namespace App\Notifications\Concerns;

use App\Notifications\Channels\FcmChannel;

trait DeliversTokenPushNotification
{
    /**
     * @return list<string|class-string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (config('fcm.enabled', true)) {
            $channels[] = FcmChannel::class;
        }

        return $channels;
    }

    /**
     * @param  array<string, string|int|bool|null>  $data
     * @return array{title: string, body: string, data: array<string, string|int|bool|null>}
     */
    protected function pushMessage(string $title, string $body, array $data = []): array
    {
        return [
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ];
    }
}
