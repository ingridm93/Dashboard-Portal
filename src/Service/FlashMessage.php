<?php

namespace App\Service;

class FlashMessage
{

    public function add($type, $message)
    {
            return $_SESSION['flashMessages'][$type][] = $message;
    }

    public function get($type)
    {
        if (!isset($_SESSION['flashMessages'])) {
            return;
        }
        $flashMessage = $_SESSION['flashMessages'][$type];

        unset($_SESSION['flashMessages'][$type]);

        return $flashMessage;
    }

    public function has($type)
    {

        if(isset($_SESSION['flashMessages'][$type])) {

            return $_SESSION['flashMessages'][$type];
        }
    }

    public function all()
    {
        if (!isset($_SESSION['flashMessages'])) {
            return;
        }

        return $_SESSION['flashMessages'];
    }
}