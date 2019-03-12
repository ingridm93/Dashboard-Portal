<?php

namespace App\Entity\EntityTrait;

use App\Service\UserProvider;

trait UpdatedByAwareTrait {

    public function addUser(User $user) {

        return $userProvider->getCurrentUser();
    }
}