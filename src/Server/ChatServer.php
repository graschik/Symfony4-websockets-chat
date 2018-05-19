<?php

namespace App\Server;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\RequestInterface;
use Ratchet\Http\HttpServerInterface;
use Ratchet\ConnectionInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class ChatServer implements HttpServerInterface
{
    protected $clients;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->clients = new \SplObjectStorage;
        $this->entityManager = $entityManager;
    }

    public function onOpen(ConnectionInterface $conn, RequestInterface $request = null)
    {
        try {
            print("new conection (" . $conn->Session->get('current_user_id') . ")");
            // Store the new connection to send messages to later
            $this->clients->attach($conn);

            echo "New connection! ({$conn->resourceId})\n";
        } catch (\Throwable $exception) {
            echo $exception->getMessage() . ' Shokovo!';
        }
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        $user = $this->entityManager
            ->getRepository(User::class)
            ->find($from->Session->get('current_user_id'));

        $user->setLastActivity(new DateTime());

        $pattern = "/^((https?|ftp)\:\/\/)?([a-z0-9]{1})((\.[a-z0-9-])|([a-z0-9-]))*\.([a-z]{2,6})(\/?)$/";
        $replace = "<a href=\"\\0\">\\0</a>";
        $result = preg_replace($pattern, $replace, $msg);
        echo $result;

        //$array=json_decode($msg);

        $message['message'] = $result;
        $message['username'] = $user->getUsername();

        foreach ($this->clients as $client) {
            // The sender is not the receiver, send to each client connected
            $client->send(json_encode($message));
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}