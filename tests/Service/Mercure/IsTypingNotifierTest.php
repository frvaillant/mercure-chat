<?php

namespace App\Tests\Service\Mercure;

use App\Entity\User;
use App\Service\Mercure\IsTypingNotifier;
use App\Service\Mercure\Topics;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mercure\HubInterface;

class IsTypingNotifierTest extends TestCase
{
    public function testNotifyIsTypingPublishesCorrectData()
    {
        $from = $this->createConfiguredMock(User::class, [
            'getId' => 1,
            'getUsername' => 'alice'
        ]);

        $to = $this->createConfiguredMock(User::class, [
            'getId' => 2,
            'getUsername' => 'bob'
        ]);

        $hub = $this->createMock(HubInterface::class);

        $notifier = $this->getMockBuilder(IsTypingNotifier::class)
            ->setConstructorArgs([$hub])
            ->onlyMethods(['publish'])
            ->getMock();

        $expectedTopicName = sprintf(Topics::TOPICS['is_typing'], 1, 2);
        $expectedTopic = 'https://localhost/topics/' . $expectedTopicName;

        $_ENV['APP_BASE_URL'] = 'https://localhost';

        $notifier->expects($this->once())
            ->method('publish')
            ->with(
                [$expectedTopic],
                [
                    'topic' => Topics::TOPICS['is_typing'],
                    'mode' => 'start',
                    'from' => 1,
                    'fromName' => 'alice',
                ]
            );

        $notifier->notifyIsTyping($from, $to, 'start');
    }
}
