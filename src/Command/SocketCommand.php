<?php

namespace App\Command;

use App\Server\ChatServer;
use App\Service\PdoSessionHandlerService;
use Doctrine\ORM\EntityManagerInterface;
use Ratchet\Session\SessionProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;


class SocketCommand extends Command
{
    private $entityManager;

    private $dbOptions;

    /**
     * SocketCommand constructor.
     * @param EntityManagerInterface $entityManager
     * @param null $name
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        $name = null
    )
    {
        parent::__construct($name);

        $this->entityManager = $entityManager;
        $this->dbOptions = [
            'db_table' => '',
            'db_id_col' => 'sess_id',
            'db_data_col' => 'sess_data',
            'db_time_col' => 'sess_time',
            'db_lifetime_col' => 'sess_lifetime',
            'lock_mode' => 0,
        ];
    }

    protected function configure()
    {
        $this->setName('sockets:start-chat')
            ->setHelp("Starts the chat socket demo")
            ->setDescription('Starts the chat socket demo')
            ->addArgument('host')
            ->addArgument('dbName')
            ->addArgument('dbUser')
            ->addArgument('dbPassword')
            ->addArgument('dbTable');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln([
            'Chat socket',
            '============',
            'Starting chat, open your browser.',
        ]);

        $this->dbOptions['db_table'] = $input->getArgument('dbTable');

        $pdoSessionHandler = new PdoSessionHandlerService(
            $input->getArgument('host'),
            $input->getArgument('dbName'),
            $input->getArgument('dbUser'),
            $input->getArgument('dbPassword'),
            $this->dbOptions
        );

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new SessionProvider(
                        new ChatServer($this->entityManager),
                        $pdoSessionHandler->getPdoSessionHandler()
                    )
                )
            ),
            8080
        );

        $server->run();
    }
}