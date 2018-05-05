<?php

namespace App\Command;

use App\Server\ChatServer;
use Doctrine\Bundle\DoctrineCacheBundle\Tests\Functional\Fixtures\Memcached;
use Doctrine\DBAL\Driver\PDOException;
use Memcache;
use PDO;
use Ratchet\Session\SessionProvider;
use React\EventLoop\Factory;
use React\Socket\Server;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// Include ratchet libs
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcachedSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

// Change the namespace according to your bundle

class SocketCommand extends Command
{
    protected function configure()
    {
        $this->setName('sockets:start-chat')
            // the short description shown while running "php bin/console list"
            ->setHelp("Starts the chat socket demo")
            // the full command description shown when running the command with
            ->setDescription('Starts the chat socket demo');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Chat socket',// A line
            '============',// Another line
            'Starting chat, open your browser.',// Empty line
        ]);

        try {
            $dbHost = 'localhost';
            $dbName = 'sessions';
            $dbUser = 'mysql';
            $dbPassword = 'mysql';
            $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo $exception->getMessage();
            die;
        }

        $dbOptions = [
            'db_table' => 'sessions',
            'db_id_col' => 'sess_id',
            'db_data_col' => 'sess_data',
            'db_time_col' => 'sess_time',
            'db_lifetime_col' => 'sess_lifetime',
            'lock_mode' => 0,
        ];

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new SessionProvider(
                        new ChatServer(),
                        new PdoSessionHandler($pdo, $dbOptions)
                    )
                )
            ),
            8080
        );

        $server->run();

    }
}