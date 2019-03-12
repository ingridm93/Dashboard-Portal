<?php

namespace App\Event;


use App\KernelEvent;
use App\Service\Firewall;
use App\Service\FlashMessage;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FirewallEventListener {

    private $firewall;
    private $flashMessage;

    public function __construct(Firewall $firewall, FlashMessage $flashMessage)
    {
        $this->firewall = $firewall;
        $this->flashMessage = $flashMessage;
    }

    public function onKernelRequest(KernelEvent $event) {

        $kernel = $event->getKernel();
        $request = $event->getRequest();
        $router = $event->getRouter();

        $userProvider = $kernel->container->get('user_provider');
        $user = $userProvider->getCurrentUser();

        if(!$this->firewall->firewall($kernel, $request, $router)) {

            if($user->getId()) {

                $this->flashMessage->add('danger', 'you do not have access to that page');
                $response = new RedirectResponse($router->generate('dashboard'));
                return $response;
            } else {
                $response = new RedirectResponse($router->generate('register'));
            }
            $event->setResponse($response);

        }
        return;
    }


}