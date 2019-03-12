<?php

namespace App\Service\RoleManager;



use Symfony\Component\HttpFoundation\Request;

class AdminManager extends AbstractUser {

    public function adminAddCourse($inputData)

    {
        //@TODO how to remove the request->all() bcz it leads to undefined indexes if not present.
        if ($inputData) {

            if ($this->courseDataIsValid($inputData)) {
                return $this->createCourse($inputData);
            }
        }
    }

    public function teacherListByCourse($skill)
    {
        $teacherList = $this->userRepository->getTeacherListBySkill($skill);

        return $this->sendResponse($teacherList);
    }

    protected function courseDataIsValid($inputData)
    {

        $courseInputData = $this->getInputData(
            $inputData,
            ['courseName', 'teacher-id','course-level', 'course-code', 'course-duration', 'session', 'status']
        );

        if(!$courseInputData['courseName'] || !$courseInputData['course-level'] || !$courseInputData['course-code'] || !$courseInputData['course-duration'] || !$courseInputData['session'] || !$courseInputData['status']) {
            $this->flashMessage->add('warning', 'all fields must be filled');
        }

        return $courseInputData['courseName'] && $courseInputData['course-level'] && $courseInputData['course-code'] && $courseInputData['course-duration'] && $courseInputData['session'] && $courseInputData['status'];
    }

    protected function createCourse($inputData)
    {
        $courseInputData = $this->getInputData($inputData,
            ['courseName', 'course-level', 'course-code', 'course-duration', 'session', 'status', 'teacher-id']
        );

        if ($this->courseRepository->checkIfCourseExists($courseInputData['courseName'], $courseInputData['course-level'], $courseInputData['course-code'], $courseInputData['course-duration'], $courseInputData['session'], $courseInputData['status']) && $courseInputData['teacher-id']) {
            $course = $this->courseManager->createCourse($courseInputData['courseName'], $courseInputData['course-level'], $courseInputData['course-code'], $courseInputData['course-duration'], $courseInputData['session'], $courseInputData['status']);

            return $this->createSectionPerTeacher($courseInputData['teacher-id'], $course);
        } else {
            $this->flashMessage->add('danger', 'this course already exists');
        }
    }

    protected function createSectionPerTeacher($teachers, $course)
    {
           $this->courseManager->createSection($teachers, $course);
    }

    public function validateTeacherId($teacherId)
    {
        if ($teacherId) {
            foreach ($teacherId as $id) {

                if (intval($id) === 0) {
                    $this->flashMessage->add('danger', 'no teacher available');
                    return $id ? false : true;
                }
                return $id;
            }
        }
    }
}