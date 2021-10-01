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

    private $shoogleId;

    private $channel;

    /**
     * StreamService constructor.
     * @param int $shoogleId
     */
    public function __construct(int $shoogleId)
    {
        $this->shoogleId = $shoogleId;
        $this->serverClient = new StreamClient(config('stream.stream_api_key'), config('stream.stream_api_secret'));
    }

    /**
     * Creating a channel for shoogle.
     *
     * @throws \GetStream\StreamChat\StreamException
     */
    public function createChannelForShoogle()
    {
        $this->channel = $this->serverClient->Channel('messaging', 'shoogleCommunity' . $this->shoogleId);
    }

    /**
     * Creating a channel for buddies.
     *
     * @param int $idOfFirstUser
     * @param int $idOfSecondUser
     * @throws \GetStream\StreamChat\StreamException
     */
    public function createChannelForBuddy(int $idOfFirstUser, int $idOfSecondUser)
    {
        $this->channel = $this->serverClient->Channel('messaging', 'shoogle' . $this->shoogleId . 'Buddy' . $idOfFirstUser . 'with' . $idOfSecondUser);
    }

    /**
     * Creating a tunnel for shoogle.
     */
    public function createTunnelForShoogle()
    {
        $this->channel->create('user' . Auth()->user()->id, ['user1' ,'user' . Auth()->user()->id]);
    }

    /**
     * Creating a tunnel for buddies.
     *
     * @param int $idOfFirstUser
     * @param int $idOfSecondUser
     */
    public function createTunnelForBuddy(int $idOfFirstUser, int $idOfSecondUser)
    {
        $this->channel->create('user' . Auth()->user()->id, ['user' . $idOfFirstUser, 'user' . $idOfSecondUser]);
    }

    /**
     * Get Chat ID.
     *
     * @return string|null
     * @throws \GetStream\StreamChat\StreamException
     */
    public function getChannelId(): ?string
    {
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
