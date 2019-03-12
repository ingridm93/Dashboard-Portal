<?php

namespace App\Service;

use App\Entity\Skill;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\PersistentCollection;
use PDO;
use PDOException;


class UserManager
{

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function createUser($firstName, $lastName, $email, $username, $password, $role)
    {

        $hashedPassword = $this->hashPassword($password);

        $user = new User();
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setRole($role);
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setPassword($hashedPassword);


        $this->em->persist($user);
        $this->em->flush();

        $this->userSession($user->getId());
        return $user;
    }


    public function createSkill($teacher, $skills)
    {
        /** @var PersistentCollection $skillRep */
        $skillList = $this->em->getRepository(Skill::class)->findById($skills);

        foreach($skillList as $skill) {
            $teacher->addSkills($skill);
        }
        $this->em->persist($teacher);
        $this->em->flush();
    }

    protected function hashPassword($password)
    {
        return sha1($password);
    }

    protected function userSession($userId)
    {
        $_SESSION['id'] = $userId;
    }

    public function registerStudentInSection($user, $section) {

        $user->addSectionToStudent($section);

        $this->em->persist($user);
        $this->em->flush();

    }




}