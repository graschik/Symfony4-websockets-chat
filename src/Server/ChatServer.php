<?php

namespace App\Server;

use App\Entity\Message;
use App\Entity\User;
use App\Service\ChatServerService;
use App\Service\DoctrineReconnectHelper;
use App\Service\UserService;
use Doctrine\DBAL\Driver\PDOException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Ratchet\Http\HttpServerInterface;
use Ratchet\ConnectionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ChatServer implements HttpServerInterface
{
    protected $clients;

    private $entityManager;

    private $userService;

    private $chatServerService;

    private $validator;

    private $doctrineReconnect;

    private $doctrine;

    /**
     * ChatServer constructor.
     * @param ContainerInterface $container
     * @param ValidatorInterface $validator
     */
    public function __construct(ContainerInterface $container, ValidatorInterface $validator)
    {
        $this->doctrine = $container->get('doctrine');
        $this->clients = new \SplObjectStorage;
        $this->entityManager = $this->doctrine->getManager();
        $this->validator = $validator;
        $this->userService = new UserService($this->entityManager);
        $this->chatServerService = new ChatServerService($this->entityManager, $this->userService);
        $this->doctrineReconnect = new DoctrineReconnectHelper($this->entityManager);
    }

    /**
     * @param ConnectionInterface $conn
     * @param RequestInterface|null $request
     */
    public function onOpen(ConnectionInterface $conn, RequestInterface $request = null): void
    {
        $this->clients->attach($conn);
        $this->sendInformationToUsers(
            $this->chatServerService->getUsersOnlineInformation($this->clients)
        );
    }

    /**
     * @param ConnectionInterface $conn
     * @param string $msg
     * @throws \Throwable
     */
    public function onMessage(ConnectionInterface $conn, $msg): void
    {
        echo "MESSAGE!";
        $attempt = 2;
        call:
        try {
            $attempt--;
            $message = $this->chatServerService->getMessage($conn, $msg);
            $errors = $this->validator->validate($message);
            dump($message);
            if (count($errors) == 0) {
                $this->entityManager->persist($message);
                $this->entityManager->flush();
            } else {
                dump($errors);
                return;
            }

            $information = $this->chatServerService->getInformationForSending($conn, $message);
            $this->sendInformationToUsers($information);

        } catch (\Throwable $exception) {
            echo "EXC!!!";
            echo $exception->getMessage();
            if (!$attempt) {
                return;
                //throw $exception;
            }
            $this->entityManager = $this->doctrine->resetManager();
            goto call;
        }
    }

    /**
     * @param string $information
     */
    public function sendInformationToUsers(string $information): void
    {
        foreach ($this->clients as $client) {
            $client->send($information);
        }
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn): void
    {
        $this->clients->detach($conn);
        $this->sendInformationToUsers(
            $this->chatServerService->getUsersOnlineInformation($this->clients)
        );
    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}