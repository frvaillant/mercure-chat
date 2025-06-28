<?php

namespace App\Service\Mercure;

use App\Entity\Message;
use Symfony\Component\Mercure\HubInterface;

class MessageNotifier extends AbstractMercurePublisher
{
    public function __construct(HubInterface $hub)
    {
        parent::__construct($hub);
    }

    /**
     * @param Invoice $invoice
     * @return void
     */
    public function notifyMessage(Message $message): void
    {

        $topic = Topics::getTopic(sprintf(
            Topics::TOPICS['message'],
            $message->getIsTo()->getId(),
            $message->getIsFrom()->getId()
        ));

        $this->publish([$topic], [
            'topic' => Topics::TOPICS['message'],
            'from' => $message->getIsFrom()->getId(),
            'fromName' => $message->getIsFrom()->getUsername(),
            'id' => $message->getId(),
            'date' => $message->getDate(),
            'text' => $message->getText(),
            'class' => 'from'
        ]);


        $topic2 = Topics::getTopic(sprintf(
            Topics::TOPICS['messages'],
            $message->getIsTo()->getId(),
        ));

        $this->publish([$topic2], [
            'topic' => Topics::TOPICS['messages'],
            'from' => $message->getIsFrom()->getId(),
            'fromName' => $message->getIsFrom()->getUsername(),
        ]);

    }

}
