<?php

namespace App\Service\RoleManager;


use App\Entity\User;

class StudentManager extends AbstractUser {

    public function getSectionsByCourse($course) {

        if($course) {

            if(!$row = $this->courseRepository->getCourseData($course)) {
               $this->flashMessage->add('danger', 'no courses were found');
            }

           return $row;
            }
    }


    protected function checkIfStudentEnrolled(User $user, $sectionId) {

        return $this->userRepository->checkIfStudentEnrolled($user->getId(), $sectionId);
    }



    public function registerStudentPerSection(User $user, $sectionId) {


        if($this->checkIfStudentEnrolled($user, $sectionId)) {

            $user;

            $this->flashMessage->add('success', 'you are now registered');

            $section = $this->sectionRepository->getSectionById($sectionId);
            return $this->userRepository->addUserToSection($user, $section);

        }

        $this->flashMessage->add('warning', 'you are already enrolled in this course');

    }
}