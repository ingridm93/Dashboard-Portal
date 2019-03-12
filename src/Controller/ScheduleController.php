<?php

namespace App\Controller;

use App\Repository\SectionRepository;
use App\Repository\TimetableRepository;
use App\Repository\UserRepository;
use App\Service\CourseManager;
use App\Service\FlashMessage;
use App\Service\RoleManager\TeacherManager;
use App\Service\UserManager;
use App\Service\UserProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

class ScheduleController extends AbstractController
{

    private $userManager;
    private $userProvider;
    private $timeInterval;
    private $margin;
    private $courseManager;
    private $weekdays;
    private $flashMessage;
    private $router;
    private $teacher;
    private $timetableRepository;
    private $userRepository;
    private $sectionRepository;


    public function __construct(UserManager $userManager, CourseManager $courseManager, TeacherManager $teacher, UserProvider $userProvider, $timeInterval, $margin, $weekdays, FlashMessage $flashMessage, Router $router, TimetableRepository $timetableRepository, UserRepository $userRepository, SectionRepository $sectionRepository)
    {
        $this->userManager = $userManager;
        $this->courseManager = $courseManager;
        $this->teacher = $teacher;
        $this->userProvider = $userProvider;
        $this->timeInterval = $timeInterval;
        $this->margin = $margin;
        $this->weekdays = $weekdays;
        $this->flashMessage = $flashMessage;
        $this->router = $router;
        $this->timetableRepository = $timetableRepository;
        $this->userRepository = $userRepository;
        $this->sectionRepository = $sectionRepository;

    }

    public function schedule()
    {

        $schedule = $this->setMargin();

        return $this->render('schedule.html.twig', ['schedule' => $schedule]);
    }

    protected function getSchedule()
    {
        $user = $this->userProvider->getCurrentUser();


        $rows = $this->timetableRepository->getUserTimetable($user);


        $arr = [
            'Monday' => [],
            'Tuesday' => [],
            'Wednesday' => [],
            'Thursday' => [],
            'Friday' => []
        ];

        $weekdays = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday'
        ];

        foreach ($rows as $row) {


            $query = http_build_query([
                'courseId' => $row['courseId'],
                'sectionId' => $row['sectionId'],
                'course' => $row['courseName']
            ]);


            if(!isset($row['timetable'])) {
                continue;
            }

            foreach ($row['timetable'] as $timetable) {

                $start = new \DateTime($timetable->getTimeStart()->format("H:i"));
                $end = new \DateTime($timetable->getTimeEnd()->format("H:i"));

                $hrToMin = intval($start->diff($end)->format("%H")) * 60;
                $totalMin = $hrToMin + intval($start->diff($end)->format("%I"));
                $height = ($totalMin / $this->timeInterval) * $this->margin;

                foreach ($weekdays as $weekday) {

                    if ($weekday === $timetable->getDay()) {

                        $arr[$weekday][] = [
                            'courseId' => $row['courseId'],
                            'sectionId' => $row['sectionId'],
                            'course' => $row['courseName'],
                            'day' => $timetable->getDay(),
                            'start' => $start->format("H:i"),
                            'end' => $end->format("H:i"),
                            'height' => $height,
                            'query' => $query
                        ];
                    }
                }
            }
        }
        return $arr;
    }

    protected function setMargin()
    {

        $arr = $this->getSchedule();

        foreach ($arr as $key => &$weekdays) {
            $prevStartTime = new \DateTime('08:00:00');

            foreach ($weekdays as &$weekday) {

                $start = new \DateTime($weekday['start']);

                $marginTop = $this->calculateMarginTop($start, $prevStartTime);
                $weekday['marginTop'] = $marginTop;

                $prevStartTime = new \DateTime($weekday['end']);
            }
        }
        return $arr;
    }

    protected function calculateMarginTop(\DateTime $startTime, \DateTime $firstStartTime)
    {
        $timeToFirstStartTime = intval($firstStartTime->diff($startTime)->format("%H")) * 60;
        $totalMarginMin = $timeToFirstStartTime + intval($firstStartTime->diff($startTime)->format("%I"));
        $marginTop = ($totalMarginMin / $this->timeInterval) * $this->margin;

        return $marginTop;
    }

    public function sectionTimetable(Request $request)
    {

        $sectionId = $request->query->get('sectionId');
        $timetable = $this->timetableRepository->getTimetable($sectionId);

        $query = parse_url($request->getRequestUri())['query'];

        return $this->render('update-schedule.html.twig', [
            'timetable' => $timetable,
            'course' => $request->query->get('course'),
            'sectionId' => $sectionId,
            'query' => $query,
            'weekdays' => $this->weekdays
        ]);
    }

    public function editSectionTimeSlots(Request $request)
    {

        $user = $this->userProvider->getCurrentUser();

        $parseUrl = parse_url($request->getRequestUri())['query'];
        parse_str($parseUrl, $queryParams);

        if ($request->request->get('weekday') !== "" && $request->request->get('timeStart') && $request->request->get('timeEnd')) {
            $newTimeSlot = $request->request->all();

            $sectionId = $queryParams['sectionId'];
            $timeslotId = $queryParams['timeslotId'];
            $weekday = $newTimeSlot['weekdays'];
            $timeStart = $newTimeSlot['timeStart'];
            $timeEnd = $newTimeSlot['timeEnd'];

            if ($this->teacher->checkForConflictAndCreateTimetable($user, $weekday, $timeStart, $timeEnd, $sectionId, $timeslotId, "update")) {

                $this->flashMessage->add('success', 'you have changed the timeslot for this section');
                return new RedirectResponse($this->router->generate('section_timetable', $queryParams));

            }
            return new RedirectResponse($this->router->generate('section_timetable', $queryParams));

        }


        return $this->render('update-time.html.twig', ['weekdays' => $this->weekdays, 'query' => $parseUrl]);
    }


    public function deleteTimeslot(Request $request)
    {

        $parseUrl = parse_url($request->getRequestUri())['query'];
        parse_str($parseUrl, $queryParams);

        $this->courseManager->deleteSectionTimeslot($queryParams['timeslotId']);
        return new RedirectResponse($this->router->generate('section_timetable', $queryParams));
    }

    public function studentSectionDelete(Request $request)
    {

        $user = $this->userProvider->getCurrentUser();
        $sectionId = $request->request->get('sectionId');
        $student = $this->userRepository->find($user->getId());
        $section = $this->sectionRepository->find($sectionId);

        $this->courseManager->deleteStudentFromSection($student, $section);

        return new RedirectResponse($this->router->generate('schedule'));
    }
}

