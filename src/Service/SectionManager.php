<?php

namespace App\Service;

use App\Entity\SectionAnnouncement;
use App\Repository\SectionAnnouncementRepository;
use Doctrine\ORM\EntityManager;

class SectionManager {


    private $announcementRepository;
    private $em;

    public function __construct(EntityManager $em) {

//        $this->announcementRepository = $announcementRepository;
        $this->em = $em;

    }


    public function teacherAddAnnouncement($sectionId) {

        $announcement = new SectionAnnouncement();
        $announcement->setSection($this->em->find(Section::class, $sectionId));



    }




}