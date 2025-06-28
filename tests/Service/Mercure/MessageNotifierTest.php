<?php

namespace App\Tests\Service\Mercure;

use App\Entity\Message;
use App\Entity\User;
use App\Service\Mercure\MessageNotifier;
use App\Service\Mercure\Topics;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mercure\HubInterface;

class MessageNotifierTest extends TestCase
{
    public function testNotifyMessagePublishesCorrectPayload()
    {
        $_ENV['APP_BASE_URL'] = 'https://localhost';

        $from = $this->createConfiguredMock(User::class, [
            'getId' => 1,
            'getUsername' => 'alice'
        ]);

        $to = $this->createConfiguredMock(User::class, [
            'getId' => 2,
            'getUsername' => 'bob'
        ]);

        $message = $this->createMock(Message::class);
        $message->method('getId')->willReturn(42);
        $message->method('getText')->willReturn('Hello Bob!');
        $message->method('getDate')->willReturn('2025-07-09 11:00:00');
        $message->method('getIsFrom')->willReturn($from);
        $message->method('getIsTo')->willReturn($to);

        $hub = $this->createMock(HubInterface::class);

        $notifier = $this->getMockBuilder(MessageNotifier::class)
            ->setConstructorArgs([$hub])
            ->onlyMethods(['publish'])
            ->getMock();

        $expectedTopic = 'https://localhost/topics/message/2/1';

        $notifier->expects($this->once())
            ->method('publish')
            ->with(
                [$expectedTopic],
                [
                    'topic' => Topics::TOPICS['message'],
                    'from' => 1,
                    'fromName' => 'alice',
                    'id' => 42,
                    'date' => '2025-07-09 11:00:00',
                    'text' => 'Hello Bob!',
                    'class' => 'from'
                ]
            );

        $notifier->notifyMessage($message);
    }
}
