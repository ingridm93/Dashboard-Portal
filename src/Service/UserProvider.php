<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManager;

class UserProvider {

    private $user;
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getCurrentUser() {

        if($this->user === null) {
            if (!isset($_SESSION['id'])) {
                $this->user = new User();
            } else {
                $this->user = $this->em->find('App\Entity\User', $_SESSION['id']);
            }
        }
        return $this->user;
    }
}