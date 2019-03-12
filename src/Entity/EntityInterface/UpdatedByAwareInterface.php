<?php

namespace App\Entity\EntityInterface;


use App\Entity\User;

interface UpdatedByAwareInterface {

    public function setUpdatedBy(User $user);

}