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
            'firebase_config' => array(
                'server_key' => config('stream.server_key'),
                'notification_template' => '{"title": "{{ channel.name }}","body": "{{ message.text }}","image": "{{ channel.imageUrl }}","click_action": "FLUTTER_NOTIFICATION_CLICK"}',
                'data_template' => '{"typeofChannel": "{{ channel.typeofChannel }}", "shoogleId": "{{ channel.shoogleId }}"}',
            ),
        ];
        $serverClient->updateAppSettings($settings);
    }
}
