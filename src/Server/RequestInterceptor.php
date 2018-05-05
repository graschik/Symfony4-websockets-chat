<?php
namespace App\Server;

use Psr\Http\Message\RequestInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServerInterface;

class RequestInterceptor implements HttpServerInterface {

    protected $clients;
    private $delegate;

    public function __construct(HttpServerInterface $delegate) {
        $this->delegate = $delegate;
    }

    public function onOpen(ConnectionInterface $conn, RequestInterface $request = null) {
        $this->delegate->onOpen($conn, $request);
    }

    public function onMessage(ConnectionInterface $from, $msg) {

    }

    public function onClose(ConnectionInterface $conn) {

    }

    public function onError(ConnectionInterface $conn, \Exception $e) {

    }
}