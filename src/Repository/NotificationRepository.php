<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class NotificationRepository extends EntityRepository {


    public function getNewNotifications($userId) {

        $newNotificationsCount = $this->createQueryBuilder("n")
            ->select("count(n.id)")
            ->join("n.appNotification", "a")
            ->join("n.user", "u")
            ->where("a.status = 'new' AND u.id = :userId" )
            ->setParameter(":userId", $userId)
            ->getQuery()
            ->getSingleScalarResult();

        return $newNotificationsCount;

    }

    public function getFiveMostRecentNotifications($userId) {

        $recentNotifications = $this->createQueryBuilder("n")
            ->select("a.body, a.status")
            ->join("n.appNotification", "a")
            ->join("n.user", "u")
            ->where("u.id = :userId")
            ->setParameter(":userId", $userId)
            ->setMaxResults(5)
            ->getQuery()
            ->getArrayResult();

        return $recentNotifications;
    }
}