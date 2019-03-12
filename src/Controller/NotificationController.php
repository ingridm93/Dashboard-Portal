<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\UserRepository;

class NotificationController {

    private $notification;
    private $userRepository;

    public function __construct(Notification $notification, UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->notification = $notification;
    }






}