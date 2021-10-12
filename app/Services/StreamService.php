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
    public function createChannelForShoogle(String $shoogleTitle)
    {
        $channel = $this->serverClient->Channel(
            'messaging',
            'shoogleCommunity' . $this->shoogleId,
            [
                'name' => $shoogleTitle,
                'shoogleId' => $this->shoogleId,
                'typeofChannel' => 'community'
            ]
        );

        $channel->create('user' . Auth()->user()->id, ['systemuser' ,'user' . Auth()->user()->id]);
        return $channel->id;
    }

    /**
     * Creating a channel for buddies.
     *
     * @param int $idOfFirstUser
     * @param int $idOfSecondUser
     * @return string|null
     * @throws \GetStream\StreamChat\StreamException
     */
    public function createChannelForBuddy(String $shoogleTitle, int $idOfFirstUser, int $idOfSecondUser)
    {
        $channel = $this->serverClient->Channel(
            'messaging',
            'shoogle' . $this->shoogleId . 'Buddy' . $idOfFirstUser . 'with' . $idOfSecondUser,
            [
                'name' => $shoogleTitle,
                'shoogleId' => $this->shoogleId,
                'typeofChannel' => 'buddy'
            ]
        );

        $channel->create('user' . Auth()->user()->id, ['user' . $idOfFirstUser, 'user' . $idOfSecondUser]);
        return $channel->id;
    }

    /**
     * Creating a channel for journal.
     *
     * @throws \GetStream\StreamChat\StreamException
     */
    public function createJournalChannel()
    {
        $userId = Auth()->user()->id;
        $channel = $this->serverClient->Channel('messaging', 'shoogle' . $this->shoogleId . 'Journal' . $userId);
        $channel->create('user' . $userId, ['user' . $userId, 'systemuser']);
        return $channel->id;
    }

    /**
     * Connect user to channel
     *
     * @param String|null $chatId
     * @throws \GetStream\StreamChat\StreamException
     */
    public function connectUserToChannel(?string $chatId)
    {
        if ($chatId == null) {
            $chatId = 'shoogleCommunity' . $this->shoogleId;
        }
        $userId = Auth()->user()->id;
        $channel = $this->serverClient->Channel('messaging', $chatId);
        $channel->addMembers(['user' . $userId]);
    }
}
