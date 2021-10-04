<?php

namespace App\Helpers;

use GetStream\StreamChat\Client as StreamClient;

/**
 * Class HelperStream
 * @package App\Helpers
 */
class HelperStream
{
    /**
     * Updating chat service settings.
     *
     * @throws \GetStream\StreamChat\StreamException
     */
    public static function init()
    {
        $serverClient = new StreamClient(config('stream.stream_api_key'), config('stream.stream_api_secret'));
        $settings = [
            'firebase_config' => [
                'server_key' => config('stream.server_key'),
//                'notification_template' => `{"message":{"notification":{"title":"New messages","body":"You have {{ unread_count }} new message(s) from {{ sender.name }}"},"android":{"ttl":"86400s","notification":{"click_action":"OPEN_ACTIVITY_1"}}}}`,
//                'data_template' => `{"sender":"{{ sender.id }}","channel":{"type": "{{ channel.type }}","id":"{{ channel.id }}"},"message":"{{ message.id }}"}`,
            ],
        ];
        $serverClient->updateAppSettings($settings);
    }
}
