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
                'notification_template' => `{"message":{"notification":{"title":"{{ channel.name }}","body":"{{ message.text }}"},"android":{"ttl":"86400s","notification":{"click_action":"FLUTTER_NOTIFICATION_CLICK"}}}}`,
                'data_template' => `{"shoogleId": "{{ channel.shoogleId }}", "type": "{{ channe.type }}"}`,
            ],
        ];
        $serverClient->updateAppSettings($settings);
    }
}
