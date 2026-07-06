<?php

namespace App\Providers;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\WebpushConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\AndroidConfig;

class FcmServiceProvider
{
    public $messaging;

    public function __construct()
    {
        $this->messaging = app('firebase.messaging');
    }

    /**
     * Send notification to a specific device
     */
    public function sendToDevice($token, $title, $body, $imageUrl = null, $data = [], $clickAction = null)
    {
        $message = CloudMessage::withTarget('token', $token)
            ->withNotification([
                'title' => $title,
                'body' => $body,
                'image' => $imageUrl,
            ])
            ->withData($data);

        $androidConfig = AndroidConfig::fromArray([
            'notification' => [
                'icon' => 'ic_launcher',
                'color' => '#20295C',
                'click_action' => $clickAction ?: 'FLUTTER_NOTIFICATION_CLICK',
            ],
        ]);


        $webpushConfig = WebpushConfig::fromArray([
            'notification' => [
                'icon' => $imageUrl ?: env('APP_LOGO'),
                'badge' => config('app.badge_url', ''),
                'click_action' => $clickAction ?: url('/'),
            ],
            'fcm_options' => [
                'link' => $clickAction ?: url('/'),
            ],
        ]);


        $apnsConfig = ApnsConfig::fromArray([
            'headers' => [
                'apns-priority' => '10',
            ],
            'payload' => [
                'aps' => [
                    'alert' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'badge' => 1,
                    'sound' => 'default',
                    'mutable-content' => 1,
                ],
                'fcm_options' => [
                    'image' => $imageUrl,
                ],
            ],
        ]);

        $message = $message
            ->withAndroidConfig($androidConfig)
            ->withWebpushConfig($webpushConfig)
            ->withApnsConfig($apnsConfig);

        return $this->messaging->send($message);
    }


    public function sendMulticast(array $tokens, $title, $body, $imageUrl = null, $data = [], $clickAction = null)
    {
        $resolvedImage = $imageUrl ?: env('APP_LOGO');

        $message = CloudMessage::new()
            ->withNotification([
                'title' => $title,
                'body' => $body,
                'image' => $resolvedImage,
            ])
            ->withData($data);

        $androidConfig = AndroidConfig::fromArray([
            'notification' => [
                'icon' => 'ic_launcher', // Local drawable asset name
                'color' => '#20295C',
                'image' => $resolvedImage, // Correct property for web-based image URLs
                'click_action' => $clickAction ?: 'FLUTTER_NOTIFICATION_CLICK',
            ],
        ]);

        $webpushConfig = WebpushConfig::fromArray([
            'notification' => [
                'icon' => env('APP_LOGO'), // Brand logo as a thumbnail
                'image' => $imageUrl,       // High-resolution image if provided
                'badge' => config('app.badge_url', ''),
                'click_action' => $clickAction ?: url('/'),
            ],
            'fcm_options' => [
                'link' => $clickAction ?: url('/'),
            ],
        ]);

        $apnsConfig = ApnsConfig::fromArray([
            'headers' => [
                'apns-priority' => '10',
            ],
            'payload' => [
                'aps' => [
                    'alert' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'badge' => 1,
                    'sound' => 'default',
                    'mutable-content' => 1,
                ],
                'fcm_options' => [
                    'image' => $resolvedImage,
                ],
            ],
        ]);

        $message = $message
            ->withAndroidConfig($androidConfig)
            ->withWebpushConfig($webpushConfig)
            ->withApnsConfig($apnsConfig);

        return $this->messaging->sendMulticast($message, $tokens);
    }
}
