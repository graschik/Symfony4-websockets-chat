<?php

namespace App\Service;


use PDO;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

class SessionHandler extends PdoSessionHandler
{
    private $pdoOrDsn;

    private $options;

    private $dbConnection;

    /**
     * SessionHandler constructor.
     * @param array $dbConnection
     * @param null $pdoOrDsn
     * @param array $options
     */
    public function __construct(array $dbConnection, $pdoOrDsn = null, array $options = array())
    {
        parent::__construct($pdoOrDsn, $options);

        $this->dbConnection = $dbConnection;
        $this->pdoOrDsn = $pdoOrDsn;
        $this->options = $options;
    }

    protected function doRead($sessionId)
    {
        try {
            return parent::doRead($sessionId);
        } catch (\Throwable $exception) {
            $this->pdoOrDsn = new PDO(
                "mysql:host=$this->dbConnection['host'];dbname=$this->dbConnection['dbName']",
                $this->dbConnection['dbUser'],
                $this->dbConnection['dbPassword'],
                [
                    PDO::ATTR_PERSISTENT => true
                ]);

            $this->pdoOrDsn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            parent::__construct($this->pdoOrDsn, $this->options);
            return parent::doRead($sessionId);
        }
    }
}