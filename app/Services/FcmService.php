<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Throwable;

class FcmService
{
    public function isConfigured(): bool
    {
        if (! config('fcm.enabled', true)) {
            return false;
        }

        $credentials = config('firebase.projects.app.credentials')
            ?? env('FIREBASE_CREDENTIALS');

        return filled($credentials) && is_readable($this->resolveCredentialsPath((string) $credentials));
    }

    /**
     * @param  array{title: string, body?: string, data?: array<string, string|int|bool|null>}  $payload
     */
    public function sendToTopic(string $topic, array $payload): void
    {
        if (! $this->isConfigured()) {
            return;
        }

        try {
            $messaging = app(Messaging::class);
        } catch (Throwable $exception) {
            Log::warning('FCM messaging unavailable.', ['message' => $exception->getMessage()]);

            return;
        }

        $message = $this->buildMessage($payload)
            ->withChangedTarget('topic', $topic);

        try {
            $messaging->send($message);
        } catch (MessagingException|FirebaseException $exception) {
            Log::error('FCM topic send failed.', [
                'topic' => $topic,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * @param  list<string>  $tokens
     * @param  array{title: string, body?: string, data?: array<string, string|int|bool|null>}  $payload
     */
    public function sendToTokens(array $tokens, array $payload): void
    {
        $tokens = array_values(array_unique(array_filter($tokens)));

        if ($tokens === [] || ! $this->isConfigured()) {
            return;
        }

        try {
            $messaging = app(Messaging::class);
        } catch (Throwable $exception) {
            Log::warning('FCM messaging unavailable.', ['message' => $exception->getMessage()]);

            return;
        }

        $message = $this->buildMessage($payload);

        try {
            $report = $messaging->sendMulticast($message, $tokens);
        } catch (MessagingException|FirebaseException $exception) {
            Log::error('FCM multicast failed.', ['message' => $exception->getMessage()]);

            return;
        }

        foreach ($report->invalidTokens() as $invalidToken) {
            DeviceToken::query()->where('token', $invalidToken)->delete();
        }
    }

    /**
     * @param  array{title: string, body?: string, data?: array<string, string|int|bool|null>}  $payload
     */
    protected function buildMessage(array $payload): CloudMessage
    {
        $notification = Notification::create(
            $payload['title'],
            $payload['body'] ?? '',
        );

        $data = collect($payload['data'] ?? [])
            ->map(fn ($value) => $value === null ? '' : (string) $value)
            ->all();

        return CloudMessage::new()
            ->withNotification($notification)
            ->withData($data)
            ->withAndroidConfig($this->androidConfig($payload));
    }

    /**
     * @param  array{title: string, body?: string, data?: array<string, string|int|bool|null>}  $payload
     */
    protected function androidConfig(array $payload): AndroidConfig
    {
        return AndroidConfig::fromArray([
            'priority' => 'high',
            'notification' => [
                'channel_id' => config('fcm.android_channel_id', 'high_importance_channel'),
                'sound' => 'default',
                'default_vibrate_timings' => true,
                'notification_priority' => 'PRIORITY_HIGH',
            ],
        ]);
    }

    protected function resolveCredentialsPath(string $path): string
    {
        if ($path !== '' && $path[0] !== '/') {
            return base_path($path);
        }

        return $path;
    }
}
