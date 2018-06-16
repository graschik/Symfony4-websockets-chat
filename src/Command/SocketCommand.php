<?php

namespace App\Command;

use App\Server\ChatServer;
use App\Service\PdoSessionHandlerService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Ratchet\Session\SessionProvider;
use Symfony\Bridge\Doctrine\Validator\DoctrineInitializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class SocketCommand extends Command
{
    private $entityManager;

    private $validator;

    private $dbOptions;

    private $dbConnection;

    /**
     * SocketCommand constructor.
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @param null $name
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        $name = null
    )
    {
        parent::__construct($name);

        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->dbOptions = [
            'db_table' => 'sessions',
            'db_id_col' => 'sess_id',
            'db_data_col' => 'sess_data',
            'db_time_col' => 'sess_time',
            'db_lifetime_col' => 'sess_lifetime',
            'lock_mode' => 0,
        ];
        $this->dbConnection = [
            'host' => 'localhost',
            'dbName' => 'sessions',
            'dbUser' => 'mysql',
            'dbPassword' => 'mysql',
        ];
    }

    protected function configure()
    {
        $this->setName('sockets:start-chat')
            ->setHelp("Starts the chat socket demo")
            ->setDescription('Starts the chat socket demo');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        $pdoSessionHandler = new PdoSessionHandlerService(
            $this->dbConnection,
            $this->dbOptions
        );

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new SessionProvider(
                        new ChatServer($this->entityManager, $this->validator),
                        $pdoSessionHandler->getPdoSessionHandler()
                    )
                )
            ),
            8080
        );

        $io->success([
            'ChatServer was successfully launched',
            'Starting chat, open your browser.',
        ]);

        $server->run();
    }
}