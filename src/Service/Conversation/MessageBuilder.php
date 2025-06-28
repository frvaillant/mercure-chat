<?php

namespace App\Service\Conversation;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class MessageBuilder
{

    private Message $message;
    public function __construct(private readonly EntityManagerInterface $manager)
    {

    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }



    /**
     * @param User $from
     * @param User $to
     * @return void
     */
    public function buildMessage(User $from, User $to): void
    {
        $message = new Message();
        $message
            ->setIsFrom($from)
            ->setIsTo($to)
        ;
        $this->message = $message;
    }

    /**
     * @param string $text
     * @return bool
     */
    public function finalize(string $text): bool
    {
        $this->message->setText($text);
        return $this->save();
    }

    /**
     * @return bool
     */
    private function save(): bool
    {
        try {
            $this->manager->persist($this->message);
            $this->manager->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }





}
