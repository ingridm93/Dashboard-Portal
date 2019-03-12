<?php
namespace App;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;

class KernelEvent extends Event {

    private $kernel;
    private $request;
    private $router;
    private $response = NULL;

    public function __construct(Kernel $kernel, Request $request, Router $router)
    {
        $this->kernel = $kernel;
        $this->request = $request;
        $this->router = $router;
    }

    /**
     * @return Kernel
     */
    public function getKernel(): Kernel
    {
        return $this->kernel;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return RequestContext
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $response
     */
    public function setResponse($response): void
    {
        $this->response = $response;
    }

    public function hasResponse() {

        if(NULL !== $this->response) {
            return true;
        }

        return false;
    }

}