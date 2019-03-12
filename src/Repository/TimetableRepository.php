<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class TimetableRepository extends EntityRepository
{


    public function checkTeacherSchedule(User $user, $weekday)
    {
        $sections = [];

        if($user->getRole() == 'student') {
            $sections = $user->getStudentSections();
        } else if ($user->getRole() === 'teacher') {
            $sections = $user->getTeacherSections();
        }

        $timetableList = [];

        foreach ($sections as $section) {

            $timetables = $section->getTimetable();

            foreach ($timetables as $timetable) {
                $timeslot = $this->findOneBy(['day' => $weekday, 'id' => $timetable->getId()]);

                if($timeslot) {
                    array_push($timetableList, $timeslot);
                }
            }
        }
        return $timetableList;
    }

    public function getUserTimetable(User $user)
    {

        $rows = [];
        $sectionsPerUserId = [];

        if($user->getRole() == 'student') {
            $sectionsPerUserId = $user->getStudentSections();
        } else if ($user->getRole() === 'teacher') {
            $sectionsPerUserId = $user->getTeacherSections();
        }

        foreach ($sectionsPerUserId as $section) {
            $course = $section->getCourse();
            $timetables = $section->getTimetable();
            $courseName =  $course->getCourseName() . $course->getCourseCode() . $course->getDuration() . "1-" . $course->getSession();


            $rows[$section->getId()] = [
                'courseId' => $course->getId(),
                'sectionId' => $section->getId(),
                'courseName' => $courseName
            ];

            foreach ($timetables as $timetable) {
                $rows[$section->getId()]['timetable'][] = $timetable;
            }
        }
        return $rows;
    }

    public function getTimetable($sectionId)
    {
        return $this->createQueryBuilder("t")
            ->join("t.section", "s")
            ->where("s.id = :sectionId")
            ->setParameter(":sectionId", $sectionId)
            ->getQuery()
            ->getArrayResult();
    }

}