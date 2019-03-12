<?php
namespace App\TwigExtension;

use App\Service\UserProvider;
use Twig_Extension;
use Twig_Extension_GlobalsInterface;


class GlobalUserTwigExtension extends Twig_Extension implements Twig_Extension_GlobalsInterface {

    private $userProvider;
    private $user;

    public function __construct (UserProvider $userProvider) {

        $this->userProvider = $userProvider;
        $this->user = $this->userProvider->getCurrentUser();
    }

    public function getGlobals()
    {
        if(!$this->user)  {
            return [];
        }
        return array(
            'user' =>      $this->user,
            'firstName' => $this->user->getFirstName(),
            'lastName' => $this->user->getLastName(),
            'role' => $this->user->getRole()
        );
    }
}