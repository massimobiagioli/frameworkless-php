<?php

namespace Untitled\Modules\Handshake\Controllers;

use Http\Request;
use Http\Response;

class HandshakeController
{
    private $request;
    private $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function handshake()
    {
        $this->response->addHeader('Content-Type', 'text/plain');
        $this->response->setStatusCode(200);
        $this->response->setContent("HANDSHAKE OK");
    }

}
