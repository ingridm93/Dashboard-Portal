<?php

namespace App\TwigExtension;

use Twig_Extension;
use Twig_Function;
use App\Service\FlashMessage;

class FlashMessageExtension extends Twig_Extension {

    private $flashMessage;

    public function __construct(FlashMessage $flashMessage)
    {
        $this->flashMessage = $flashMessage;
    }


    public function getFunctions()
    {
        return array(
            new Twig_Function(
                'getFlashMessage', function ($flash) {
                return $this->flashMessage->get($flash);
            }),
            new Twig_Function('hasFlashMessage', function ($type) {
                return $this->flashMessage->has($type);
            }),
            new Twig_Function('flashes', function () {
                return $this->flashMessage->all();
            })
        );
    }

    public function getName() {
        return 'flash_message_extension';
    }

}