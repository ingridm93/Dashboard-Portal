<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class SectionRepository extends EntityRepository
{

    public function getSectionById($sectionId)
    {

        return $this->find($sectionId);
    }

    public function getSectionListPerCourse($courseId)
    {
        return $this->createQueryBuilder("s")
            ->select("s")
            ->join("s.course", "c")
            ->where("c.id = :courseId")
            ->setParameter(":courseId", $courseId)
            ->getQuery()
            ->getResult();

    }

    public function findSectionPerUser ($sectionId) {

       return $this->createQueryBuilder("s")
            ->join("s.teacher", "u")
            ->where("s.id = :sectionId")
            ->setParameter(":sectionId", $sectionId)
            ->getQuery()
            ->getResult();

    }

    public function getStudentsPerSection($sectionId)
    {
        return $this->createQueryBuilder("s")
            ->select("u.firstName, u.lastName")
            ->join("s.students", "u")
            ->where("s.id = :sectionId")
            ->setParameter(":sectionId", $sectionId)
            ->getQuery()
            ->getResult();
    }
}

