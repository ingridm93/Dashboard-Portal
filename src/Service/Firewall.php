<?php

namespace App\Service;

use App\Entity\User;
use App\KernelEvent;


class Firewall
{

    private $flashMessage;

    public function __construct(FlashMessage $flashMessage, UserProvider $userProvider)
    {

        $this->flashMessage = $flashMessage;

    }

    public function firewall($kernel, $request, $router)
    {

        $requestContext = $kernel->requestContext($request, $router);

        $userProvider = $kernel->container->get('user_provider');
        $user = $userProvider->getCurrentUser();

        if ($user->getId() && !isset($requestContext['_role'])) {
            return true;
        }

        if (!$this->hasAccess($user, $requestContext)) {

            if ($user->getId()) {
                return false;
            } else {
                return false;

            }
        }
        return true;
    }


    public function hasAccess(User $user, $requestContext)
    {

        if (isset($requestContext['_role']) && !in_array($user->getRole(), $requestContext['_role'])) {
            return false;
        }

        if(!isset($requestContext['_role'])) {
            return false;
        }

        return true;
    }

    protected function accessibleByAll($requestContext)
    {
        if (!isset($requestContext['_role'])) {
            return true;
        }
    }
}