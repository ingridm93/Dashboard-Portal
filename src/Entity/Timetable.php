<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Timetable
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\TimetableRepository")
 * @ORM\Table(name="timetable")
 */

class Timetable {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\ManyToOne(targetEntity="Section", inversedBy="timetable", cascade={"persist"})
     */
    public $section;
    /**
     * @ORM\Column(name="day", type="string", length=100)
     */

    public $day;

    /**
     * @ORM\Column(name="timeStart", type="time")
     */
    public $timeStart;

    /**
     * @ORM\Column(name="timeEnd", type="time")
     */
    public $timeEnd;



    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
        $section->addTimetable($this);
        $this->section = $section;
    }

    /**
     * @return mixed
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param mixed $day
     */
    public function setDay($day)
    {
        $this->day = $day;
    }

    /**
     * @return mixed
     */
    public function getTimeStart()
    {
        return $this->timeStart;
    }

    /**
     * @param mixed $timeStart
     */
    public function setTimeStart($timeStart)
    {
        $this->timeStart = $timeStart;
    }

    /**
     * @return mixed
     */
    public function getTimeEnd()
    {
        return $this->timeEnd;
    }

    /**
     * @param mixed $timeEnd
     */
    public function setTimeEnd($timeEnd)
    {
        $this->timeEnd = $timeEnd;
    }
}
