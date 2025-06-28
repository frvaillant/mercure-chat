<?php

namespace App\Tests\Service\Conversation;

use App\Entity\Message;
use App\Entity\User;
use App\Service\Conversation\ConversationDataTransformer;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class ConversationDataTransformerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testGetMessageData()
    {
        $user = $this->createMock(User::class);
        $from = $this->createConfiguredMock(User::class, [
            'getUsername' => 'alice'
        ]);
        $to = $this->createConfiguredMock(User::class, [
            'getUsername' => 'bob'
        ]);

        $message = $this->createMock(Message::class);
        $message->method('getId')->willReturn(1);
        $message->method('getText')->willReturn('Hello!');
        $message->method('getDate')->willReturn('2025-07-09 10:00:00');
        $message->method('getClass')->with($user)->willReturn('from');
        $message->method('getIsFrom')->willReturn($from);
        $message->method('getIsTo')->willReturn($to);

        $result = ConversationDataTransformer::getMessageData($message, $user);

        $this->assertEquals([
            'id' => 1,
            'text' => 'Hello!',
            'date' => '2025-07-09 10:00:00',
            'class' => 'from',
            'from' => 'alice',
            'to' => 'bob',
        ], $result);
    }

    /**
     * @throws Exception
     */
    public function testGetConversationDataAsArray()
    {
        $user = $this->createMock(User::class);

        $message1 = $this->createMock(Message::class);
        $message1->method('getId')->willReturn(1);
        $message1->method('getText')->willReturn('Hi');
        $message1->method('getDate')->willReturn('2025-07-09 10:00:00');
        $message1->method('getClass')->willReturn('from');
        $message1->method('getIsFrom')->willReturn($this->mockUser('alice'));
        $message1->method('getIsTo')->willReturn($this->mockUser('bob'));

        $message2 = $this->createMock(Message::class);
        $message2->method('getId')->willReturn(2);
        $message2->method('getText')->willReturn('Hello');
        $message2->method('getDate')->willReturn('2025-07-09 10:05:00');
        $message2->method('getClass')->willReturn('to');
        $message2->method('getIsFrom')->willReturn($this->mockUser('bob'));
        $message2->method('getIsTo')->willReturn($this->mockUser('alice'));

        $result = ConversationDataTransformer::getConversationData([$message1, $message2], $user, true);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('Hi', $result[1]['text']);
        $this->assertEquals('Hello', $result[2]['text']);
    }

    public function testGetConversationDataAsJson()
    {
        $user = $this->createMock(User::class);

        $message = $this->createMock(Message::class);
        $message->method('getId')->willReturn(1);
        $message->method('getText')->willReturn('Test message');
        $message->method('getDate')->willReturn('2025-07-09 10:00:00');
        $message->method('getClass')->willReturn('from');
        $message->method('getIsFrom')->willReturn($this->mockUser('alice'));
        $message->method('getIsTo')->willReturn($this->mockUser('bob'));

        $json = ConversationDataTransformer::getConversationData([$message], $user, false);

        $this->assertJson($json);
        $array = json_decode($json, true);
        $this->assertEquals('Test message', $array[1]['text']);
        $this->assertEquals('bob', $array[1]['to']);
    }

    private function mockUser(string $username): User
    {
        $mock = $this->createMock(User::class);
        $mock->method('getUsername')->willReturn($username);
        return $mock;
    }
}
