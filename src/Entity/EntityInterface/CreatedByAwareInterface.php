<?php

namespace App\Entity\EntityInterface;



use App\Entity\EntityInterface\TimestampableInterface;
use App\Entity\EntityInterface\UpdatedByAwareInterface;
use App\Entity\User;

interface CreatedByAwareInterface extends TimestampableInterface, UpdatedByAwareInterface {

    public function setCreatedBy(User $user);

}