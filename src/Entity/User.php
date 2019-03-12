<?php
namespace App\Entity;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class User
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="user")
 */
class User {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\Column(name="role", type="string", length=100)
     */
    public $role = 'unauthenticated';

    /**
     * @ORM\Column(name="firstName", type="string", length=100)
     */
    public $firstName;

    /**
     * @ORM\Column(name="lastName", type="string", length=100)
     */
    public $lastName;

    /**
     * @ORM\Column(name="email", type="string", length=100)
     */
    public $email;

    /**
     * @ORM\Column(name="username", type="string", length=100)
     */
    public $username;

    /**
     * @ORM\Column(name="password", type="string", length=100)
     */
    public $password;

    /**
     * @ORM\ManyToMany(targetEntity="Skill", inversedBy="teacher")
     */
    public $skills;

    /**
     * @ORM\ManyToMany(targetEntity="Section", inversedBy="students")
     */
    public $studentSections;

    /**
     * @ORM\OneToMany(targetEntity="Section", mappedBy="teacher")
     */
    public $teacherSections;

    public function __construct()
    {
        $this->studentSections = new ArrayCollection();
        $this->teacherSections = new ArrayCollection();
        $this->skills = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        $this->getPassword();
    }

    public function addSkills(Skill $skills)
    {
        $skills->setTeacher($this);
        $this->skills[] = $skills;
    }

    public function getSkills()
    {
        return $this->skills;
    }

    public function addSectionToStudent (Section $sections)
    {
        $sections->addStudents($this);
        $this->studentSections[] = $sections;
    }

    public function getStudentSections()
    {
        return $this->studentSections;
    }

    public function addSectionToTeacher (Section $section)
    {
        $this->teacherSections[] = $section;
    }

    public function getTeacherSections ()
    {
        return $this->teacherSections;
    }
}