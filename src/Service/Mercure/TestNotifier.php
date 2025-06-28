<?php

namespace App\Service\Mercure;

use App\Entity\User;
use Symfony\Component\Mercure\HubInterface;

class TestNotifier extends AbstractMercurePublisher
{
    public function __construct(HubInterface $hub)
    {
        parent::__construct($hub);
    }

    /**
     * @param Invoice $invoice
     * @return void
     */
    public function notifyTest(int $userId, bool $isSuccessfull): void
    {

        $topic = sprintf(Topics::getTopic(Topics::TOPICS['test']), $userId);

        $this->publish([$topic], [
            'topic' => Topics::TOPICS['test'],
            'success' => $isSuccessfull
        ]);
    }

}
