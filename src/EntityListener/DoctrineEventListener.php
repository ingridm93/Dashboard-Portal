<?php
namespace App\EntityListener;

use App\Entity\EntityInterface\CreatedByAwareInterface;
use App\Entity\EntityTrait\UpdatedByAwareTrait;
use App\Service\UserProvider;
use Doctrine\ORM\Event\PreUpdateEventArgs ;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;



class DoctrineEventListener {

    private $userProvider;

    public function __construct(UserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    use UpdatedByAwareTrait;


    function getCurrentTime() {

       return new \DateTime();
    }

    function createdBy() {

        return $this->userProvider->getCurrentUser();
    }

    public function prePersist(LifecycleEventArgs $args)
    {

        $entity = $args->getObject();

        if ($entity instanceof CreatedByAwareInterface) {

            $entity->setCreatedBy($this->createdBy());
            $entity->setCreatedAt($this->getCurrentTime());
        }
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {

        $entity = $args->getObject();

        if ($entity instanceof CreatedByAwareInterface) {

            $entity->setUpdatedBy($this->createdBy());
            $entity->setUpdatedAt($this->getCurrentTime());
        }
    }
}