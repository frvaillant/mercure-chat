<?php

namespace App\Service\Mercure;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

abstract class AbstractMercurePublisher
{

    public function __construct(private HubInterface $hub)
    {
    }

    protected function publish(array $topics, array $data): void
    {
        $update = new Update(
            $topics,
            json_encode($data)
        );
        try {
            $this->hub->publish($update);
        } catch (\Exception $e) {
            //Todo Log erreur
        }
    }
}
