<?php

namespace App\Service;


use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;

class DoctrineReconnectHelper
{
    private $entityManager;

    public function __construct(ObjectManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function disconnect()
    {
        $this->entityManager->getConnection()->close();
    }

    public function connect()
    {
        $res = $this->entityManager->getConnection()->connect();
        dump('CONNECT '.$res);
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
dump('PING');
            $this->disconnect();
            $this->connect();
            dump($this->entityManager->isOpen());
            $this->checkEMConnection();
        }
        return $this->entityManager;
    }

    /**
     * method checks connection and reconnect if needed
     * MySQL Server has gone away
     *
     * @throws \Doctrine\ORM\ORMException
     */
    protected function checkEMConnection()
    {
        if (!$this->entityManager->isOpen()) {
            dump('NOT OPEN');
            $config = $this->entityManager->getConfiguration();

            $this->entityManager->create(
                $this->entityManager->getConnection(), $config
            );
            dump('RECREATE EM');
        }
    }
}