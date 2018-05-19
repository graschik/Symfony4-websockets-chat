<?php

namespace App\Service;


use PDO;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

class SessionHandler extends PdoSessionHandler
{
    private $pdoOrDsn;

    private $options;

    /**
     * SessionHandler constructor.
     * @param null $pdoOrDsn
     * @param array $options
     */
    public function __construct($pdoOrDsn = null, array $options = array())
    {
        parent::__construct($pdoOrDsn, $options);

        $this->pdoOrDsn = $pdoOrDsn;
        $this->options = $options;
    }

    protected function doRead($sessionId)
    {
        try {
            echo 'lol';
            return parent::doRead($sessionId);
        } catch (\Throwable $exception) {
            $this->pdoOrDsn = new PDO(
                "mysql:host=localhost;dbname=sessions",
                'mysql',
                'mysql',
                [
                    PDO::ATTR_PERSISTENT => true
                ]);

            $this->pdoOrDsn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            parent::__construct($this->pdoOrDsn, $this->options);
            return parent::doRead($sessionId);
        }
    }
}