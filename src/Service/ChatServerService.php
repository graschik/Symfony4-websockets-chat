<?php

namespace App\Service;


use App\Entity\Message;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Ratchet\ConnectionInterface;

class ChatServerService
{
    private $entityManager;

    private $userService;

    public const MESSAGE_TOPIC = 'message';

    public const USERS_ONLINE = 'users_online';

    public const USER_ID = 'current_user_id';

    /**
     * ChatServerService constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserService $userService
     */
    public function __construct(EntityManagerInterface $entityManager, UserService $userService)
    {
        $this->entityManager = $entityManager;
        $this->userService = $userService;
    }

    /**
     * @param ConnectionInterface $connection
     * @param Message $message
     * @return string
     */
    public function getInformationForSending(ConnectionInterface $connection, Message $message): string
    {
        $user = $this->userService->getUserById(
            $connection->Session->get(self::USER_ID)
        );

        $information['topic'] = self::MESSAGE_TOPIC;
        $information['username'] = $user->getUsername();
        $information['message'] = $message->getText();
        $information['date'] = $message->getDate();

        return json_encode($information);
    }

    /**
     * @param ConnectionInterface $connection
     * @param string $text
     * @return Message
     */
    public function getMessage(ConnectionInterface $connection, string $text): Message
    {
        $message = new Message();
        $message->setText($this->prepareText($text));
        $message->setDate((new \DateTime())->format('Y-m-d'));
        $message->setUserId($connection->Session->get(self::USER_ID));

        return $message;
    }

    /**
     * @param \SplObjectStorage $connections
     * @return string
     */
    public function getUsersOnlineInformation(\SplObjectStorage $connections): string
    {
        $information['topic'] = self::USERS_ONLINE;

        $connections = $this->getUniqueUsers($connections);

        foreach ($connections as $connection) {
            $user = $this->entityManager
                ->getRepository(User::class)
                ->find(
                    $connection->Session->get(self::USER_ID)
                );
            $information['users'][] = $user->getUsername();
        }
        return json_encode($information);
    }

    /**
     * @param \SplObjectStorage $connections
     * @return \SplObjectStorage
     */
    public function getUniqueUsers(\SplObjectStorage $connections): \SplObjectStorage
    {
        $uniqueUsers = new \SplObjectStorage();

        foreach ($connections as $connection) {
            foreach ($uniqueUsers as $uniqueUser) {
                if ($uniqueUser->Session->get(self::USER_ID) == $connection->Session->get(self::USER_ID)) {
                    break 2;
                }
            }
            $uniqueUsers->attach($connection);
        }

        return $uniqueUsers;
    }

    /**
     * @param string $text
     * @return string
     */
    public function prepareText(string $text): string
    {
        $text = trim($text);

        $text = str_replace(array("\r\n", "\r", "\n"), "<br/>", $text);

        $func = function ($matches) {
            if (preg_match('/https?/ix', $matches[1])) {
                $link = $matches[1];
                return "<a href=\"$link\">$link</a>";
            } else {
                $link = $matches[1];
                return "<a href=\"http://$link\">$link</a>";
            }
        };

        return preg_replace_callback(
            '/(?=(([\w\/\/:.]+)\.(?:com|net|org|info|no|dk|se|by|ru)))\b(?:(?:https?|ftp|file):\/\/|(?:www\.|ftp\.)?)
            (?:\([-A-Z0-9+&@#\/%=~_|$?!:,.]*\)|[-A-Z0-9+&@#\/%=~_|$?!:,.])*
            (?:\([-A-Z0-9+&@#\/%=~_|$?!:,.]*\)|[A-Z0-9+&@#\/%=~_|$])/ix',
            $func,
            $text
        );
    }
}