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
     * Create a connection on a remote chat server.
     *
     * @param int|null $shoogleId
     * @return string|null
     * @throws \GetStream\StreamChat\StreamException
     */
    public static function getChatId(?int $shoogleId): ?string
    {
        if ( is_null($shoogleId) ) {
            return null;
        }

        $serverClient = new StreamClient(config('stream.stream_api_key'), config('stream.stream_api_secret'));
        $channel = $serverClient->Channel('messaging', 'shoogleCommunity' . $shoogleId);
        $channel->create('user' . Auth()->user()->id);

        return $channel->id;
    }
}
