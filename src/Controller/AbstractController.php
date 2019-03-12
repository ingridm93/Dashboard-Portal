<?php
namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

class AbstractController {

    protected $twig;

    /**
     * @param mixed $twig
     */
    public function setTwig(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @return mixed
     */
    public function getTwig()
    {
        return $this->twig;
    }

    protected function render(string $view, array $parameters = array(), Response $response = null) {

       $content =  $this->twig->render($view, $parameters);

        if($response === null) {

            $response = new Response();
        }

        $response->setContent($content);

        return $response;
    }

    public function ajax($data, Response $response = null) {

        if($response === null) {
            $response = new Response();

        }
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($data);
        return $response;
    }
}