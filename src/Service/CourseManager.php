<?php

namespace App\Service;

use App\Entity\Course;
use App\Entity\Section;
use App\Entity\Timetable;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;

class CourseManager
{

    /** @var EntityManager */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function createCourse($courseName, $courseLevel, $courseCode, $duration, $session, $status)
    {
        $course = new Course();
        $course->setCourseName($courseName);
        $course->setCourseCode($courseCode);
        $course->setCourseLevel($courseLevel);
        $course->setDuration($duration);
        $course->setSession($session);
        $course->setStatus($status);

        $this->em->persist($course);
        $this->em->flush();

        return $course;
    }


    public function createSection($teachers, $course)
    {
        $teacherList = $this->em->getRepository(User::class)->findById($teachers);

        foreach ($teacherList as $teacher) {

            $section = new Section();
            $section->setCourse($course);
            $section->setTeacher($teacher);

            $this->em->persist($section);
        }

        $this->em->flush();
    }

    public function createSectionTimetable($sectionId, $day, $start, $end)
    {
        $section = $this->em->find(Section::class, $sectionId);
        $timetable = new Timetable();

        $timetable->setDay($day);
        $timetable->setTimeStart(new \DateTime($start));
        $timetable->setTimeEnd(new \DateTime($end));
        $timetable->setSection($section);

        $this->em->persist($timetable);

        $this->em->flush();

    }

    public function updateTimeslot($timeslotId, $weekday, $timeStart, $timeEnd)
    {
        $timeslot = $this->em->find(Timetable::class, $timeslotId);
        $timeslot->setDay($weekday);
        $timeslot->setTimeStart(new \DateTime($timeStart));
        $timeslot->setTimeEnd(new \DateTime($timeEnd));

        $this->em->persist($timeslot);

        $this->em->flush();
    }



    public function deleteSectionTimetable($sectionId)
    {

        $timetables = $this->em->find(Section::class, $sectionId)->getTimetable();

        foreach ($timetables as $timetable) {

            $this->em->remove($timetable);

        }
        $this->em->flush();
    }

    public function deleteSectionTimeslot($timeslotId)
    {

        $timeslot = $this->em->find(Timetable::class, $timeslotId);

        $this->em->remove($timeslot);
        $this->em->flush();
    }

    public function updateTeacherPerSection($teacherId, $sectionId) {

        $section = $this->em->find(Section::class, $sectionId);
        $newTeacher = $this->em->find(User::class, $teacherId);
        $section->setTeacher($newTeacher);

        $this->em->persist($section);
        $this->em->flush();
    }

    public function deleteStudentFromSection(User $student, Section $section) {

        $student->getStudentSections()->removeElement($section);

        $this->em->persist($student);
        $this->em->flush();
    }


}
