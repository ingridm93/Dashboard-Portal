<?php

namespace App;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;
use Twig_Environment;
use Twig_Loader_Filesystem;

class Kernel
{

    /** @var ContainerInterface */
    public $container;

    public function __construct()
    {
        $this->buildContainer();
        $this->buildRouter();
    }

    protected function buildContainer()
    {

        $this->container = new ContainerBuilder();
        $loader = new YamlFileLoader($this->container, new FileLocator(__DIR__ . '/../config'));
        $loader->load('services.yaml');

        $twig = $this->twig();
        $this->container->set('twig', $twig);

        return $this->container;
    }

    protected function buildRouter()
    {
        $locator = new FileLocator([__DIR__ . '/../config']);
        $loader = new \Symfony\Component\Routing\Loader\YamlFileLoader($locator);
        $router = new Router(
            $loader,
            'routes.yaml'
        );

        $this->container->set('router', $router);
    }

    protected function twig()
    {

        $loader = new Twig_Loader_Filesystem('twig');

        /** @var ContainerBuilder $containerBuilder */
        $containerBuilder = $this->container;


        $twig = new Twig_Environment($loader, array(//          'cache' => '/path/to/compilation_cache',
        ));

        $twigExtensionIds = $containerBuilder->findTaggedServiceIds("twig.extension");

        foreach ($twigExtensionIds as $id => $tags) {
            $twig->addExtension($containerBuilder->get($id));
        }

        return $twig;
    }


    public function requestContext(Request $request, Router $router)
    {

        $requestContext = new RequestContext();
        $requestContext->fromRequest($request);

        /** @var Router $router */

        $router->setContext($requestContext);

        $matchRequest = $router->matchRequest($request);
        $request->attributes->add($matchRequest);

        return $matchRequest;
    }

    protected function registerEventListeners(EventDispatcher $dispatcher)
    {


        $listeners = $this->container->findTaggedServiceIds('kernel.event_listener');

        foreach ($listeners as $id => $tag) {

            $listener = $this->container->get($id);

            foreach ($tag as $attr) {

                $dispatcher->addListener($attr['event'], [$listener, $attr['method']]);
            }

        }
    }

    protected function registerEntityListeners()
    {
/** @var EntityManager $em */
        $em = $this->container->get('entity_manager');

        $entityListeners = $this->container->findTaggedServiceIds('doctrine.orm.entity_listener');

        foreach($entityListeners as $id => $tag) {

            foreach ($tag as $attr) {
                $listener = $this->container->get($id);
                $em->getEventManager()->addEventListener($attr['event'], $listener);
            }
        }
    }

    public function handle(Request $request)
    {

        /** @var \Symfony\Component\Routing\Router $router */

        $router = $this->container->get('router');

        /** @var \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher */
        $dispatcher = $this->container->get('event_dispatcher');

        $controllerData = $this->requestContext($request, $router);

        $event = new KernelEvent($this, $request, $router);

        $this->registerEventListeners($dispatcher);
        $this->registerEntityListeners();

//        $firewall = $this->container->get('firewall');

//        $dispatcher->addListener('kernel.request', [$firewall, 'firewall']);
//
        $dispatcher->dispatch('kernel.request', $event);

        if ($event->hasResponse()) {
            $response = $event->getResponse();
            return $response;
        }


        $explode = explode("::", $controllerData['_controller'], 2);
        [$className, $method] = $explode;
        // instatiation of class, anything to be initialized/ any variable to be passed to a class before response should be set here.

        $instance = $this->container->get($className);
        $instance->setTwig($this->container->get('twig'));

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = call_user_func(array($instance, $method), $request);

        return $response;
    }

    public function terminate()
    {

    }
}