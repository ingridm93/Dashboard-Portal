<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class SectionAnnouncement
 * @ORM\Entity(repositoryClass="App\Repository\SectionAnnouncementRepository")
 * @ORM\Table(name="sectionAnnouncement")
 */

class SectionAnnouncement {

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="announcement", type="string", length=100)
     */

    private $announcement;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Section", inversedBy="announcements")
     */

    private $section;

    /**
     * @ORM\Column(name="timestamp", type="datetime")
     */

    private $time;

    public function __construct()
    {
        $this->setTime(new \DateTime('now'));

    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getAnnouncement()
    {
        return $this->announcement;
    }

    /**
     * @param mixed $announcement
     */
    public function setAnnouncement($announcement)
    {
        $this->announcement = $announcement;
    }

    /**
     * @return mixed
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @param mixed $section
     */
    public function setSection(Section $section)
    {
        $section->addAnnouncement($this);
        $this->section = $section;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }



}