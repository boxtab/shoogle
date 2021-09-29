<?php

namespace App\Services;

use GetStream\StreamChat\Client as StreamClient;

/**
 * Class StreamService
 * @package App\Services
 */
class StreamService
{
    private $serverClient;

    private $channel;

    /**
     * StreamService constructor.
     *
     * @param int $shoogleId
     * @throws \GetStream\StreamChat\StreamException
     */
    public function __construct(int $shoogleId)
    {
        $this->serverClient = new StreamClient(config('stream.stream_api_key'), config('stream.stream_api_secret'));
        $this->channel = $this->serverClient->Channel('messaging', 'shoogleCommunity' . $shoogleId);
    }

    /**
     * Get Chat ID.
     *
     * @return string|null
     * @throws \GetStream\StreamChat\StreamException
     */
    public function getChatId(): ?string
    {
        $this->channel->create('user' . Auth()->user()->id, ['user1' ,'user' . Auth()->user()->id]);
        return $this->channel->id;
    }

    /**
     * Join the chat.
     *
     * @throws \GetStream\StreamChat\StreamException
     */
    public function addMembers()
    {
        $this->channel->addMembers(['user' . Auth()->user()->id]);
    }
}
