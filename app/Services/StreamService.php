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
        $channel = $this->serverClient->Channel('messaging', 'shoogleCommunity' . $this->shoogleId);
        $channel->create('user' . Auth()->user()->id, ['user1' ,'user' . Auth()->user()->id]);
        return $channel->id;
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
        $channel = $this->serverClient->Channel('messaging', 'shoogle' . $this->shoogleId . 'Buddy' . $idOfFirstUser . 'with' . $idOfSecondUser);
        $channel->create('user' . Auth()->user()->id, ['user' . $idOfFirstUser, 'user' . $idOfSecondUser]);
        return $channel->id;
    }
}
