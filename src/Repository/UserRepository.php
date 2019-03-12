<?php

namespace App\Repository;

use App\Entity\Course;
use App\Entity\Section;
use App\Entity\Skill;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

    public function validateUsername($username)
    {
        $user = $this->findBy(array('username' => $username), null, 1);

        return $user ? false : true;
    }

    public function validateEmail($email)
    {
        $user = $this->findBy(array('email' => $email), null, 1);

        return $user ? false : true;
    }

    public function readUser($username, $password)
    {
        $hashedPassword = sha1($password);

        $user = $this->findOneBy([
            'username' => $username,
            'password' => $hashedPassword
        ]);

        if ($user) {
            $_SESSION['id'] = $user->getId();
            return $user->getId();
        }
    }

    public function addSectionToCourse($course)
    {

        $bio = $this->_em->getRepository(Course::class)
            ->findOneBy($course);

        $section = new Section();

        $section->setCourse($bio);
        $this->_em->persist($section);
        return $this->_em->flush();

    }

    public function addUserToSection($user, $section)
    {

        $user->addSectionToStudent($section);

        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function checkIfStudentEnrolled($userId, $sectionId)
    {

        $row = $this->createQueryBuilder("u")
            ->join("u.studentSections", "s")
            ->where("s.id = :sectionId AND u.id = :userId")
            ->setParameter(":sectionId", $sectionId)
            ->setParameter(":userId", $userId)
            ->getQuery()
            ->getArrayResult();
        $row;

        return $row ? false : true;
    }

    public function getTeacherListBySkill($id)
    {
        return $this->createQueryBuilder("u")
            ->select("partial u.{id,firstName,lastName}")
            ->join("u.skills", "s")
            ->where('s.id = :skillId')
            ->setParameter(':skillId', $id)
            ->getQuery()
            ->getArrayResult();
    }

    public function  getTeacherListBySkillName($name)
    {
        return $this->createQueryBuilder("u")
            ->select("partial u.{id,firstName,lastName}")
            ->join("u.skills", "s")
            ->where('s.name = :skillName')
            ->setParameter(':skillName', $name)
            ->getQuery()
            ->getArrayResult();
    }

    public function readTeacherSections($userId)
    {

        $user = $this->find($userId);
        $sections = $user->getTeacherSections();
        $sectionList = [];

        foreach ($sections as $section) {
            $course = $section->getCourse();
            $sectionName = $course->getCourseName() . $course->getCourseCode() . $course->getDuration() . "1-" . $course->getSession();
            $sectionList[$section->getId()] = $sectionName;
        }

        return $sectionList;
    }

    public function getAllStudents($sectionId)
    {
        $query = $this->createQueryBuilder("u")
            ->select("u.id")
            ->join("u.studentSections", "s")
            ->where("s.id = :sectionId AND u.role = 'student'")
            ->setParameter(":sectionId", $sectionId)
            ->getQuery()
            ->getDQL();

        return  $this->createQueryBuilder("n")
            ->select("partial n.{id,firstName,lastName}")
            ->where("n.id NOT IN (" . $query . ") AND n.role = 'student'")
            ->setParameter(":sectionId", $sectionId)
            ->getQuery()
            ->getResult();
        }

        public function findStudents(array $studentIds) {

        return $this->findById($studentIds);

        }
}
