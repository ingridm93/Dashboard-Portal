<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class SectionAnnouncementRepository extends EntityRepository {

    public function getAnnouncementsPerSection($sectionId) {

        return $this->createQueryBuilder("a")
            ->select("a.announcement, a.time")
            ->join("a.section", "s")
            ->join("s.teacher", "t")
            ->where("s.id = :sectionId")
            ->setParameter(":sectionId", $sectionId)
            ->orderBy("a.time", 'DESC')
            ->getQuery()
            ->getResult();
    }


}