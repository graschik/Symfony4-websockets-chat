<?php

namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;

class DoctrineReconnectHelper
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function disconnect()
    {
        $this->entityManager->getConnection()->close();
    }

    public function connect()
    {
        $this->entityManager->getConnection()->connect();
    }

    /**
     * MySQL Server has gone away
     * @param EntityManagerInterface $entityManager
     * @return EntityManagerInterface
     */
    public function reconnect(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $connection = $this->entityManager->getConnection();
        if (!$connection->ping()) {

            $this->disconnect();
            $this->connect();

            $this->checkEMConnection($connection);
        }
        return $this->entityManager;
    }

    /**
     * method checks connection and reconnect if needed
     * MySQL Server has gone away
     *
     * @param $connection
     * @throws \Doctrine\ORM\ORMException
     */
    protected function checkEMConnection($connection)
    {
        if (!$this->entityManager->isOpen()) {
            $config = $this->entityManager->getConfiguration();

            $this->entityManager = $this->entityManager->create(
                $connection, $config
            );
        }
    }
}