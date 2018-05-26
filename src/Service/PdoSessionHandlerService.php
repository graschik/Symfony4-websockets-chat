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

    private $dbConnection;

    /**
     * PdoSessionHandlerService constructor.
     * @param array $dbConnection
     * @param array $dbOptions
     */
    public function __construct(
        array $dbConnection,
        array $dbOptions
    )
    {
        $this->dbPassword = $dbConnection['dbPassword'];
        $this->dbName = $dbConnection['dbName'];
        $this->dbUser = $dbConnection['dbUser'];
        $this->host = $dbConnection['host'];
        $this->dbOptions = $dbOptions;
        $this->dbConnection = $dbConnection;

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
        return new SessionHandler($this->dbConnection, $this->pdo, $this->dbOptions);
    }

}