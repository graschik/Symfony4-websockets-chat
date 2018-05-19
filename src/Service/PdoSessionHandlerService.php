<?php

namespace App\Service;


use PDO;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

class PdoSessionHandlerService
{
    private $host;

    private $dbName;

    private $dbUser;

    private $dbPassword;

    private $pdo;

    private $dbOptions;

    /**
     * PdoSessionHandlerService constructor.
     * @param string $host
     * @param string $dbName
     * @param string $dbUser
     * @param string $dbPassword
     * @param array $dbOptions
     */
    public function __construct(
        string $host,
        string $dbName,
        string $dbUser,
        string $dbPassword,
        array $dbOptions
    )
    {
        $this->dbPassword = $dbPassword;
        $this->dbName = $dbName;
        $this->dbUser = $dbUser;
        $this->host = $host;
        $this->dbOptions = $dbOptions;

        $this->setPdoConnection();
    }

    private function setPdoConnection(): void
    {
        $this->pdo = new PDO(
            "mysql:host=$this->host;dbname=$this->dbName",
            $this->dbUser,
            $this->dbPassword,
            [
                PDO::ATTR_PERSISTENT => true
            ]);

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * @return PDO
     */
    public function getNewPdoConnection(): PDO
    {
        $this->setPdoConnection();
        return $this->pdo;
    }

    /**
     * @return SessionHandler
     */
    public function getPdoSessionHandler(): SessionHandler
    {
        return new SessionHandler($this->pdo, $this->dbOptions);
    }

}