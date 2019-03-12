<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use App\Repository\SectionRepository;
use App\Repository\SkillRepository;
use App\Repository\TimetableRepository;
use App\Repository\UserRepository;
use App\Service\CourseManager;
use App\Service\FlashMessage;
use App\Service\UserManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

class AdminCourseController extends AbstractController
{
    private $courseManager;
    private $courses;
    private $flashMessage;
    private $router;
    private $params;
    private $courseRepository;
    private $sectionRepository;
    private $userRepository;
    private $skillRepository;
    private $timetableRepository;
    private $userManager;

    public function __construct(CourseManager $courseManager, UserManager $userManager, $courses, FlashMessage $flashMessage, Router $router, CourseRepository $courseRepository, SectionRepository $sectionRepository, UserRepository $userRepository, SkillRepository $skillRepository, TimetableRepository $timetableRepository)
    {
        $this->courseManager = $courseManager;
        $this->userManager = $userManager;
        $this->courses = $courses;
        $this->flashMessage = $flashMessage;
        $this->router = $router;
        $this->courseRepository = $courseRepository;
        $this->sectionRepository = $sectionRepository;
        $this->userRepository = $userRepository;
        $this->skillRepository = $skillRepository;
        $this->timetableRepository = $timetableRepository;
    }

    public function searchCourses($params = NULL, $editFields = NULL)
    {

        $courses = $this->skillRepository->getAllSkills();

        return $this->render('admin-search.html.twig', ['courses' => $courses, 'params' => $params, 'editFields' => $editFields]);
    }

    public function listCourseSections(Request $request)
    {
        $currentPage = 1;

        if ($request->query->get('page')) {
            $currentPage = $request->query->get('page');
        }

        $filter = $request->query->all();

        $courseList = $this->getCourseList($currentPage, $filter);

        return $courseList;
    }

    protected function getCourseList($currentPage, array $courseFilter)
    {
        $this->params = http_build_query($courseFilter);
        $queryCases = ['courseName', 'courseLevel', 'courseCode', 'duration', 'session', 'status'];


        if (!$pages = $this->getPageCount($queryCases, $courseFilter)) {

            return new RedirectResponse($this->router->generate('manage_courses'));
        }
        $courseList = $this->courseRepository->getAllCoursesByFilter($currentPage, $queryCases, $courseFilter);

        return $this->searchCourses(['courseList' => $courseList, 'pages' => $pages, 'params' => $this->params]);
    }

    protected function getPageCount($queryCases, $courseFilter)
    {
        $count = $this->courseRepository->getCourseRowCount($queryCases, $courseFilter);

        if (!$count[0][1]) {

            $this->flashMessage->add('danger', 'No course with the following criteria were found');
            return;

        } else {
            $pageCount = ceil($count[0][1] / 5);
            $pageParam = $this->createPageViewParameter($pageCount);

            return $pageParam;
        }
    }

    protected function createPageViewParameter($pageCount)
    {

        $pages = [];

        for ($i = 1; $i <= $pageCount; $i++) {

            array_push($pages, $i);
        }

        return $pages;
    }


    public function editSections(Request $request)
    {
        $courseInfo = $request->query->all();
        $query = http_build_query($courseInfo);
        $sectionsInfo = $this->sectionRepository->getSectionListPerCourse($courseInfo['courseId']);

        $teacherList = $this->userRepository->getTeacherListBySkillName($courseInfo['courseName']);

        $sectionData = [];
        foreach ($sectionsInfo as $section) {
            $sectionData[] = [
                'section' => $section,
                'notEnrolledStudents' => $this->userRepository->getAllStudents($section->getId())
            ];
        }

        foreach ($sectionData as $data) {

            $students = $data['section']->getStudents();

            foreach($students as $student) {
                $role = $student->getEmail();

                $role;
            }
            $students->toArray();
            $students;
        }


        return $this->render('admin-edit-course.html.twig', [
            'courseInfo' => $courseInfo,
            'sections' => $sectionsInfo,
            'sectionData' => $sectionData,
            'teachers' => $teacherList,
            'query'=>$query
        ]);
    }

    public function deleteSection(Request $request)
    {

        $sectionId = $request->request->get('sectionId');

        $this->adminCourseManager->deleteSection($sectionId);

        $this->flashMessage->add('success', 'The section has successfully been removed');

        return new RedirectResponse($this->router->generate('manage_courses'));
    }

    public function updateSectionTeacher(Request $request)
    {

        $sectionTeacher = $request->request->all();
        $queryParams = $request->query->all();

        $this->courseManager->updateTeacherPerSection($sectionTeacher['teacherId'], $sectionTeacher['sectionId']);

        $this->courseManager->deleteSectionTimetable($sectionTeacher['sectionId']);

        $this->flashMessage->add('success', 'The teacher has been changed for that section, please advise to add a timetable.');

        return new RedirectResponse($this->router->generate('edit_sections', $queryParams));
    }

    public function addStudents (Request $request) {

        $query = $request->query->all();
        $sectionId = $request->request->get('sectionId');
        $studentIds = $request->request->get('student-id');

        $students = $this->userRepository->findStudents($studentIds);
        $section = $this->sectionRepository->getSectionById($sectionId);

        if($studentIds) {

            foreach ($students as $student) {

                if ($this->userRepository->checkIfStudentEnrolled($student->getId(), $sectionId)) {

                        $this->userManager->registerStudentInSection($student, $section);
                    }
            }
            $this->flashMessage->add('success', 'the students have been added');
            return new RedirectResponse($this->router->generate('edit_sections', $query));
        }
        return new RedirectResponse($this->router->generate('manage_courses'));

    }

    public function deleteStudent (Request $request) {

        $query = $request->query->all();
        $studentId = $request->request->get('studentId');
        $sectionId = $request->request->get('sectionId');

        $student = $this->userRepository->find($studentId);
        $section = $this->sectionRepository->find($sectionId);


        $this->courseManager->deleteStudentFromSection($student, $section);

        return new RedirectResponse($this->router->generate('edit_sections', $query));

    }
}