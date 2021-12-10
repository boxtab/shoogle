<?php

namespace App\Services;

use GetStream\StreamChat\Client as StreamClient;
use GetStream\StreamChat\StreamException;

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
     * @throws StreamException
     */
    public function createChannelForShoogle(string $shoogleTitle, string $shoogleImageUrl)
    {
        $channel = $this->serverClient->Channel(
            'messaging',
            'shoogleCommunity' . $this->shoogleId,
            [
                'name' => $shoogleTitle,
                'shoogleId' => $this->shoogleId,
                'typeofChannel' => 'community',
                'imageUrl' => $shoogleImageUrl
            ]
        );

        $channel->create('user' . Auth()->user()->id, ['systemuser', 'user' . Auth()->user()->id]);
        return $channel->id;
    }

    /**
     * Creating a channel for buddies.
     *
     * @param int $idOfFirstUser
     * @param int $idOfSecondUser
     * @return string|null
     * @throws StreamException
     */
    public function createChannelForBuddy(string $shoogleTitle, int $idOfFirstUser, int $idOfSecondUser, string $shoogleImageUrl)
    {
        $channel = $this->serverClient->Channel(
            'messaging',
            'shoogle' . $this->shoogleId . 'Buddy' . $idOfFirstUser . 'with' . $idOfSecondUser,
            [
                'name' => $shoogleTitle,
                'shoogleId' => $this->shoogleId,
                'typeofChannel' => 'buddy',
                'imageUrl' => $shoogleImageUrl
            ]
        );

        $channel->create('user' . Auth()->user()->id, ['user' . $idOfFirstUser, 'user' . $idOfSecondUser]);
        return $channel->id;
    }

    /**
     * Creating a channel for journal.
     *
     * @throws StreamException
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
     * @throws StreamException
     */
    public function connectUserToChannel(?string $chatId, ?string $message)
    {
        if ($chatId == null) {
            $chatId = 'shoogleCommunity' . $this->shoogleId;
        }
        $userId = 'user' . Auth()->user()->id;
        $channel = $this->serverClient->Channel('messaging', $chatId);
        $channel->addMembers([$userId]);
        if (isset($message)) {
            $channel->sendMessage(["text" => $message], $userId);
        }
    }

    /**
     * Connect user to channel
     *
     * @param String|null $chatId
     * @throws StreamException
     */
    public function disconnectUserFromShoogleChannels()
    {
        $userId = 'user' . Auth()->user()->id;

        $channel = $this->serverClient->Channel(
            'messaging',
            'shoogleCommunity' . $this->shoogleId
        );
        $channel->removeMembers([$userId]);

        $channel = $this->serverClient->Channel('messaging', 'shoogle' . $this->shoogleId . 'Journal' . Auth()->user()->id);
        $channel->delete();
    }

    /**
     * Connect user to channel
     *
     * @param String $chatId
     * @throws StreamException
     */
    public function removeBuddyChat(string $chatId)
    {
        $channel = $this->serverClient->Channel('messaging', $chatId);
        $channel->delete();
    }

    /**
     * @throws StreamException
     */
    public static function getChannelMembers(string $channelId)
    {
        $serverClient = new StreamClient(config('stream.stream_api_key'), config('stream.stream_api_secret'));
        $channel = $serverClient->Channel('messaging', $channelId);
        return $channel->queryMembers();
    }

    public function getChannelLastMessageDateAtString(string $channelId)
    {
        $url = "channels/messaging/" . $channelId;
        $res = $this->serverClient->post($url . "/query", ['state' => true]);
        return $res['channel']['last_message_at'] ?? null;
    }

    public function getFlagList(int $page = 1) {
        return $this->serverClient->get(
            "moderation/flags/message",
            [
                "payload" => json_encode([
                        "channel_cid" => "messaging:xyz",
                        "limit" => 10,
                        "offset" => 10 * ($page - 1)
                ])
            ]
        )['flags'];
    }
}
