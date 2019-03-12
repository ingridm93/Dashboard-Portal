<?php
namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Skill
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\SkillRepository")
 * @ORM\Table(name="skill")
 */


class Skill {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;


    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="skills")
     */
    public $teacher;

    /**
     * @ORM\Column(name="name", type="string", length=100)
     */
    public $name;
    
    public function __construct() {
        $this->teacher = new ArrayCollection();
    }

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
    public function getTeacher()
    {
        return $this->teacher;
    }

    /**
     * @param mixed $teacher
     */
    public function setTeacher(User $teacher)
    {
        $this->teacher[] = $teacher;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /** d
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

}