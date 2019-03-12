<?php

namespace App\Entity;

use App\Entity\EntityInterface\CreatedByAwareInterface;
use App\Entity\EntityTrait\CreatorAwareTrait;
use App\Repository\SectionAnnouncementRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Course
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\SectionRepository")
 * @ORM\Table(name="section")
 */
class Section implements CreatedByAwareInterface
{

    use CreatorAwareTrait;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\ManyToOne(targetEntity="Course", inversedBy="sections")
     */
    public $course;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="studentSections")
     */
    public $students;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="teacherSections")
     */
    public $teacher;

    /**
     * @ORM\OneToMany(targetEntity="Timetable", mappedBy="section")
     */
    public $timetable;

    /**
     * @ORM\OneToMany(targetEntity="SectionAnnouncement", mappedBy="section")
     */
    private $announcements;


    public function __construct()
    {

        $this->students = new ArrayCollection();
        $this->timetable = new ArrayCollection();
        $this->announcements = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * @param Course $course
     */
    public function setCourse(Course $course)
    {
        $course->addSection($this);
        $this->course = $course;
    }

    public function addStudents(User $student)
    {
        $this->students[] = $student;
    }

    public function getStudents()
    {
        return $this->students;
    }

    public function addTimetable(Timetable $timetable)
    {
        $this->timetable[] = $timetable;
    }

    public function getTimetable()
    {
        return $this->timetable;
    }

    public function setTeacher(User $teacher)
    {
        $teacher->addSectionToTeacher($this);
        $this->teacher = $teacher;
    }

    public function getTeacher()
    {
        return $this->teacher;
    }

    /**
     * @return mixed
     */
    public function getAnnouncements()
    {
        return $this->announcements;
    }

    /**
     * @param mixed $announcement
     */
    public function addAnnouncement(SectionAnnouncement $announcement)
    {
        $this->announcements[] = $announcement;
    }
}