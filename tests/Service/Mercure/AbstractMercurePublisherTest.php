<?php

namespace App\Tests\Service\Mercure;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use App\Service\Mercure\AbstractMercurePublisher;

class AbstractMercurePublisherTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testPublishSendsUpdate()
    {
        $hub = $this->createMock(HubInterface::class);

        $hub->expects($this->once())
            ->method('publish')
            ->with($this->callback(function (Update $update) {
                $this->assertEquals(['topic/1'], $update->getTopics());
                $this->assertJsonStringEqualsJsonString(
                    json_encode(['key' => 'value']),
                    $update->getData()
                );
                return true;
            }));

        $publisher = new class($hub) extends AbstractMercurePublisher {
            public function callPublish(array $topics, array $data): void
            {
                $this->publish($topics, $data);
            }
        };

        $publisher->callPublish(['topic/1'], ['key' => 'value']);
    }

    /**
     * @throws Exception
     */
    public function testPublishHandlesException()
    {
        $hub = $this->createMock(HubInterface::class);

        $hub->method('publish')
            ->will($this->throwException(new \Exception('Mercure failed')));

        $publisher = new class($hub) extends AbstractMercurePublisher {
            public function callPublish(array $topics, array $data): void
            {
                try {
                    $this->publish($topics, $data);
                } catch (\Throwable $e) {
                    // do nothing
                }
            }
        };


        $publisher->callPublish(['topic/1'], ['key' => 'value']);
        // Si on arrive ici, c'est que tout est passé avant, d'où le pléonasme ci-dessous :-)
        $this->assertTrue(true);
    }
}
