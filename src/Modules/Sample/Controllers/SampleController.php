<?php

namespace Untitled\Modules\Sample\Controllers;

use Http\Request;
use Http\Response;

class SampleController
{
    private $request;
    private $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function tester()
    {
        $this->response->addHeader('Content-Type', 'application/json');
        $this->response->setStatusCode(200);
        $this->response->setContent("asdf1234");
    }

}
