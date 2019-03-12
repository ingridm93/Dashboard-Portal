<?php
namespace App\Entity;
use App\Entity\EntityInterface\CreatedByAwareInterface;
use App\Entity\EntityTrait\CreatorAwareTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Course
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\CourseRepository")
 * @ORM\Table(name="course")
 */

class Course implements CreatedByAwareInterface
{

    use CreatorAwareTrait;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\Column(name="courseName", type="string",  length=100)
     */
    public $courseName;

    /**
     *@ORM\Column(name="courseLevel", type="integer", length=100)
     */
    public $courseLevel;
    /**
     *@ORM\Column(name="courseCode", type="integer", length=100)
     */
    public $courseCode;
    /**
     *@ORM\Column(name="duration", type="string", length=100)
     */
    public $duration;
    /**
     *@ORM\Column(name="session", type="string", length=100)
     */
    public $session;
    /**
     *@ORM\Column(name="status", type="string", length=100)
     */
    public $status;

    /**
     * @ORM\OneToMany(targetEntity="Section", mappedBy="course")
     * @var ArrayCollection An ArrayCollection of Section objects.
     */
    public $sections;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
    }


    public function getId() {

        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCourseName()
    {
        return $this->courseName;
    }

    /**
     * @param mixed $courseName
     */
    public function setCourseName($courseName)
    {
        $this->courseName = $courseName;
    }

    /**
     * @return mixed
     */
    public function getCourseLevel()
    {
        return $this->courseLevel;
    }

    /**
     * @param mixed $courseLevel
     */
    public function setCourseLevel($courseLevel)
    {
        $this->courseLevel = $courseLevel;
    }

    /**
     * @return mixed
     */
    public function getCourseCode()
    {
        return $this->courseCode;
    }

    /**
     * @param mixed $courseCode
     */
    public function setCourseCode($courseCode)
    {
        $this->courseCode = $courseCode;
    }

    /**
     * @return mixed
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param mixed $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return mixed
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param mixed $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function addSection (Section $section)
    {
        $this->sections[] = $section;
    }

    /**
     * @return ArrayCollection
     */
    public function getSections()
    {
        return $this->sections;
    }
}