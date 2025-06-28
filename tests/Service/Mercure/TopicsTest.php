<?php

namespace App\Tests\Service\Mercure;

use App\Service\Mercure\Topics;
use PHPUnit\Framework\TestCase;

class TopicsTest extends TestCase
{
    protected function setUp(): void
    {
        $_ENV['APP_BASE_URL'] = 'https://localhost';
    }

    public function testGetTopicReturnsCorrectUrl()
    {
        $topicName = 'message/1/2';
        $expected = 'https://localhost/topics/message/1/2';

        $this->assertEquals($expected, Topics::getTopic($topicName));
    }

    public function testGetTopicWithConnection()
    {
        $topicName = 'connection';
        $expected = 'https://localhost/topics/connection';

        $this->assertEquals($expected, Topics::getTopic($topicName));
    }

    public function testGetTopicWithIsTyping()
    {
        $topicName = 'is_typing/from/5/to/8';
        $expected = 'https://localhost/topics/is_typing/from/5/to/8';

        $this->assertEquals($expected, Topics::getTopic($topicName));
    }

    public function testTopicsConstantExists()
    {
        $this->assertArrayHasKey('message', Topics::TOPICS);
        $this->assertEquals('message/%d/%d', Topics::TOPICS['message']);

        $this->assertArrayHasKey('is_typing', Topics::TOPICS);
        $this->assertEquals('is_typing/from/%d/to/%d', Topics::TOPICS['is_typing']);
    }
}
