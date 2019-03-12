<?php

namespace App\Entity\EntityInterface;

interface TimestampableInterface {

    public function setCreatedAt(\DateTime $time);

    public function setUpdatedAt(\DateTime $dateTime);


}