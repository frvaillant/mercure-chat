<?php

namespace App\Service\Mercure;

use App\Entity\User;
use Symfony\Component\Mercure\HubInterface;

class IsTypingNotifier extends AbstractMercurePublisher
{
    public function __construct(HubInterface $hub)
    {
        parent::__construct($hub);
    }

    /**
     * @param Invoice $invoice
     * @return void
     */
    public function notifyIsTyping(User $from, User $to, string $mode): void
    {
        $topicName = sprintf(
            Topics::TOPICS['is_typing'],
            $from->getId(),
            $to->getId(),
        );
        $topic = Topics::getTopic($topicName);

        $this->publish([$topic], [
            'topic' => Topics::TOPICS['is_typing'],
            'mode' => $mode,
            'from' => $from->getId(),
            'fromName' => $from->getUsername(),
        ]);
    }

}
