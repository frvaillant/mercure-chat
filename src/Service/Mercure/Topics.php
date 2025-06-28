<?php

namespace App\Service\Mercure;

use App\Service\Application\App;

class Topics
{

    const TOPICS = [
        'test' => 'test/%d', // Connected user's ID
        'messages' => 'messages/%d', // message->isTo()
        'message' => 'message/%d/%d', // message->isTo() / message->isFrom()
        'is_typing' => 'is_typing/from/%d/to/%d', // message->isFrom() / message->isTo()
    ];

    /**
     * @param string $topicName
     * @return string
     */
    public static function getTopic(string $topicName): string
    {
        return $_ENV['APP_BASE_URL'] . '/topics/' . $topicName;
    }


}
