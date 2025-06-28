<?php

namespace App\Service\Conversation;

use App\Entity\Message;
use App\Entity\User;
use Symfony\Component\Form\FormInterface;

class ConversationDataTransformer
{


    /**
     * @param array $conversation
     * @param User $user
     * @param bool $asArray
     * @return array|string
     *
     * Builds an array from conversation and returns it as array or json in terms of $asArray
     */
    public static function getConversationData(array $conversation, User $user, bool $asArray = false): array | string
    {
        $conversationData = [];
        /** @var Message $message */
        foreach ($conversation as $message) {
            $conversationData[$message->getId()] = self::getMessageData($message, $user);
        }

        if($asArray) {
            return $conversationData;
        }

        return json_encode($conversationData);
    }

    /**
     * @param Message $message
     * @param User $user
     * @return array
     *
     * Get required front data from message
     */
    public static function getMessageData(Message $message, User $user): array
    {
        return [
            'id' => $message->getId(),
            'text' => $message->getText(),
            'date' => $message->getDate(),
            'class' => $message->getClass($user),
            'from' => $message->getIsFrom()->getUsername(),
            'to' => $message->getIsTo()->getUsername()
        ];
    }

}
