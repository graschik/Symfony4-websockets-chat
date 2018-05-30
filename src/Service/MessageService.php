<?php

namespace App\Service;


use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;

class MessageService
{
    private $entityManager;

    private $userService;

    public const MESSAGE_LIMIT = 10;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserService $userService
    )
    {
        $this->entityManager = $entityManager;
        $this->userService = $userService;
    }


    /**
     * @param int $messageCount
     * @return mixed
     */
    public function getPreviousMessages(int $messageCount)
    {
        if ($messageCount - self::MESSAGE_LIMIT <= 0) {
            $offset = 0;
            $limit = 0;
        } else {
            $offset = $messageCount - self::MESSAGE_LIMIT;
            $limit = self::MESSAGE_LIMIT;
        }

        return $this->entityManager
            ->getRepository(Message::class)
            ->findByOffset($limit, $offset);
    }

    /**
     * @return int
     */
    public function getMessageCount()
    {
        $messages = $this->entityManager
            ->getRepository(Message::class)
            ->findAll();

        return count($messages);
    }

    public function getLastMessages()
    {
        $messageCount = $this->getMessageCount();

        if ($messageCount - self::MESSAGE_LIMIT <= 0) {
            $offset = 0;
        } else {
            $offset = $messageCount - self::MESSAGE_LIMIT;
        }

        return $this->entityManager
            ->getRepository(Message::class)
            ->findByOffset(
                self::MESSAGE_LIMIT,
                $offset
            );
    }

    public function prepareMessagesForSending(array $messages)
    {
        $newMessage = [];
        $messageArray = [];
        foreach ($messages as $message) {
            $newMessage['message'] = $message['text'];
            $newMessage['date'] = $message['date'];
            $newMessage['username'] =
                $this->userService
                    ->getUserById($message['userId'])
                    ->getUsername();

            array_push($messageArray, $newMessage);
        }

        return $messageArray;
    }

}