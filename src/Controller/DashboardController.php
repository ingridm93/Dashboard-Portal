<?php

namespace App\Controller;

use App\Entity\Course;
use App\Repository\CourseRepository;
use App\Repository\NotificationRepository;
use App\Repository\SectionRepository;
use App\Repository\SkillRepository;
use App\Repository\UserRepository;
use App\Service\CourseManager;
use App\Service\FlashMessage;
use App\Service\RoleManager\AdminManager;
use App\Service\RoleManager\StudentManager;
use App\Service\RoleManager\TeacherManager;
use App\Service\UserProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use App\Service\Calendar;


class DashboardController extends AbstractController
{

    /** @var CourseManager */
    private $courseManager;

    /** @var Router */
    private $router;
    private $userProvider;
    protected $user;
    private $adminManager;
    private $teacherManager;
    private $studentManager;
    protected $flashMessage;
    private $weekdays;
    private $courseRepository;
    private $skillRepository;
    private $userRepository;
    private $notificationRepository;


    public function __construct(CourseManager $courseManager, Router $router, UserProvider $userProvider, AdminManager $adminManager, TeacherManager $teacherManager, StudentManager $studentManager, FlashMessage $flashMessage, $weekdays, CourseRepository $courseRepository, SkillRepository $skillRepository, UserRepository $userRepository, NotificationRepository $notificationRepository)
    {
        $this->courseManager = $courseManager;
        $this->router = $router;
        $this->userProvider = $userProvider;
        $this->adminManager = $adminManager;
        $this->teacherManager = $teacherManager;
        $this->studentManager = $studentManager;
        $this->flashMessage = $flashMessage;
        $this->weekdays = $weekdays;
        $this->courseRepository = $courseRepository;
        $this->skillRepository = $skillRepository;
        $this->userRepository = $userRepository;
        $this->notificationRepository = $notificationRepository;

    }

    public function dashboard()
    {
        $user = $this->userProvider->getCurrentUser();

        $newNotificationCount = $this->notificationRepository->getNewNotifications($user->getId());
        $newNotificationCount;

        $recentNotifications = $this->notificationRepository->getFiveMostRecentNotifications($user->getId());
        $recentNotifications;

        return $this->render('dashboard.html.twig', ['firstName' => $user->getFirstName(), 'lastName' => $user->getLastName(), 'role' => $user->getRole(), 'newNotification' => $newNotificationCount]);

    }

//    public function addSectionTimetable()
//    {
//        $section = load section\
//
//    new Timetable(
//        set the data tometable
//
//        try {
//            if(checkForConflicts($user, $timetable)) {
//                //ALERT FLASH MESSAGE
//            } else {
//                //persist timetable and flush
//                //add success flash message
//            }
//
//    } catch (Exception $e) {
//
//    }
//}
    public function handleCourse(Request $request)
    {
        $user = $this->userProvider->getCurrentUser();

        $role = $user->getRole();
        $userId = $user->getId();

        if ($role === 'admin') {

            if ($request->query->get('course') && count($request->query->all()) === 1) {

                return $this->adminManager->teacherListByCourse($request->query->get('course'));
            }

            if($this->adminManager->validateTeacherId($request->request->get('teacher-id'))) {

                $this->adminManager->adminAddCourse($request->request->all());
                $this->flashMessage->add('success', 'you have successfully added this course');
                return new RedirectResponse($this->router->generate('add_course'));
            }

            $courseList = $this->skillRepository->getAllSkills();
            return $this->render('add-course.html.twig', ['courseList' => $courseList]);

        } else if ($role === 'teacher') {

            if (count($request->request->all()) > 0) {

                $this->teacherManager->addTeacherTimetable($request->request->all());
            }
            //@TODO WHERE timetable is null;
            $sections = $this->userRepository->readTeacherSections($userId);

            return $this->render('add-timetable.html.twig', ['sections' => $sections, 'weekdays' => $this->weekdays]);

        } else {

            if ($courseList = $this->studentManager->getSectionsByCourse($request->query->get('course'))) {

                return $this->render('course-sections.html.twig', ['courseList' => $courseList]);

            } else if (null !== $request->request->get('submit')) {


                $this->studentManager->registerStudentPerSection($user, $request->request->get('sectionId'));

            }

            return $this->render('search-sections.html.twig');
        }
    }

}
