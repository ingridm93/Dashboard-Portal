<?php

require_once "./vendor/autoload.php";

use App\Entity\Course;
use App\Entity\Section;
use App\Entity\SectionAnnouncement;
use App\Entity\Skill;
use App\Entity\Notification;
use App\Entity\AppNotification;
use App\Entity\EmailNotification;
use App\Entity\User;

use App\Kernel;

$kernel = new Kernel();

$em = $kernel->container->get('entity_manager');



$courses = ['BIO', 'CHM', 'PHY', 'MTH', 'ENG', 'DEU', 'ART'];

$users = [
    [
        'firstName' => 'David',
        'lastName' => 'S',
        'email' => 'd@d',
        'username' => 'davids',
        'password' => '40bd001563085fc35165329ea1ff5c5ecbdbbeef',
        'role' => 'admin'
    ],
    [
        'firstName' => 'Sou',
        'lastName' => 'M',
        'email' => 's@s',
        'username' => 'sounm',
        'password' => '40bd001563085fc35165329ea1ff5c5ecbdbbeef',
        'role' => 'admin'
    ],
    [
        'firstName' => 'maggie',
        'lastName' => 'w',
        'email' => 'm@m',
        'username' => 'maggiew',
        'password' => '40bd001563085fc35165329ea1ff5c5ecbdbbeef',
        'role' => 'teacher',
        'skills' => ['BIO', 'CHM', 'MTH', 'DEU', 'PHY']

    ],
    [
        'firstName' => 'justus',
        'lastName' => 'k',
        'email' => 'j@j',
        'username' => 'justusk',
        'password' => '40bd001563085fc35165329ea1ff5c5ecbdbbeef',
        'role' => 'teacher',
        'skills' => ['BIO', 'CHM', 'MTH', 'ART']
    ],
    [
        'firstName' => 'ingrid',
        'lastName' => 'm',
        'email' => 'i@i',
        'username' => 'ingridm',
        'password' => '40bd001563085fc35165329ea1ff5c5ecbdbbeef',
        'role' => 'student'
    ],
    [
        'firstName' => 'erika',
        'lastName' => 'm',
        'email' => 'e@e',
        'username' => 'erikam',
        'password' => '40bd001563085fc35165329ea1ff5c5ecbdbbeef',
        'role' => 'student'
    ],
    [
        'firstName' => 'julian',
        'lastName' => 'p',
        'email' => 'j@p',
        'username' => 'julianp',
        'password' => '40bd001563085fc35165329ea1ff5c5ecbdbbeef',
        'role' => 'student'
    ]
];

$coursesList = [
    [
        'courseName' => 'MTH',
        'courseLevel' => 1,
        'courseCode' => 101,
        'duration' => 'H',
        'session' => 'F',
        'status' => 'Online'
    ],
    [
        'courseName' => 'DEU',
        'courseLevel' => 2,
        'courseCode' => 200,
        'duration' => 'Y',
        'session' => 'Y',
        'status' => 'Online'
    ],
    [
        'courseName' => 'CHM',
        'courseLevel' => 3,
        'courseCode' => 200,
        'duration' => 'H',
        'session' => 'S',
        'status' => 'Offline'
    ],
    [
        'courseName' => 'BIO',
        'courseLevel' => 2,
        'courseCode' => 202,
        'duration' => 'H',
        'session' => 'F',
        'status' => 'Online'
    ]
];

$sections = [
    [
        'courseName' => 'BIO',
        'courseLevel' => 2,
        'courseCode' => 202,
        'duration' => 'H',
        'session' => 'F',
        'status' => 'Online'
    ],
    [
        'courseName' => 'BIO',
        'courseLevel' => 2,
        'courseCode' => 202,
        'duration' => 'H',
        'session' => 'F',
        'status' => 'Online'
    ],
    [
        'courseName' => 'CHM',
        'courseLevel' => 3,
        'courseCode' => 200,
        'duration' => 'H',
        'session' => 'S',
        'status' => 'Offline'
    ],
    [
        'courseName' => 'CHM',
        'courseLevel' => 3,
        'courseCode' => 200,
        'duration' => 'H',
        'session' => 'S',
        'status' => 'Offline'
    ],
    [
        'courseName' => 'DEU',
        'courseLevel' => 2,
        'courseCode' => 200,
        'duration' => 'Y',
        'session' => 'Y',
        'status' => 'Online'
    ]
];

$skillList = [];
foreach ($courses as $course) {

    $skill = new Skill();
    $skill->setName($course);

    $skillList[$course] = $skill;
    $em->persist($skill);

}


foreach ($users as $userInfo) {

    $user = new User();


    $user->setFirstName($userInfo['firstName']);
    $user->setLastName($userInfo['lastName']);
    $user->setRole($userInfo['role']);
    $user->setEmail($userInfo['email']);
    $user->setUsername($userInfo['username']);
    $user->setPassword($userInfo['password']);

    if ($userInfo['role'] === 'teacher') {

        foreach ($userInfo['skills'] as $skill) {

            $skillPerName = $skillList[$skill];
            $user->addSkills($skillPerName);
            $em->persist($skillPerName);
        }
    }
    $em->persist($user);

}

$em->flush();

foreach ($coursesList as $item) {

    $course = new Course();

    $course->setCourseName($item['courseName']);
    $course->setCourseCode($item['courseCode']);
    $course->setCourseLevel($item['courseLevel']);
    $course->setDuration($item['duration']);
    $course->setSession($item['session']);
    $course->setStatus($item['status']);
    $course->setCreatedAt(new \DateTime());
    $course->setCreatedBy($em->find(User::class, 2));


    $em->persist($course);

}
$em->flush();


foreach ($sections as $course) {
    $bio = $em->getRepository(Course::class)
        ->findOneBy($course);

    $section = new Section();
    $section->setCourse($bio);

    $section->setCreatedAt(new DateTime());
    $section->setCreatedBy($em->find(User::class, 1));
    $em->persist($section);
}
$em->flush();

$teacher = $em->find(User::class, 2);
$section = $em->find(Section::class, 1);
$section->setTeacher($teacher);

$em->persist($section);

$student = $em->find(User::class, 5);
$student2 = $em->find(User::class, 6);
$student2->addSectionToStudent($section);
$student->addSectionToStudent($section);

$em->persist($student);
$em->persist($student2);
$em->flush();

$bioSections = $em->getRepository(Section::class)->findById([1, 2]);

$timetable = new \App\Entity\Timetable();

$timetable->setSection($bioSections[0]);
$timetable->setDay('Monday');
$timetable->setTimeStart(new \DateTime('09:00:00'));
$timetable->setTimeEnd(new \DateTime('12:00:00'));
$em->persist($timetable);

$timetable = new \App\Entity\Timetable();

$timetable->setSection($bioSections[0]);
$timetable->setDay('Wednesday');
$timetable->setTimeStart(new \DateTime('09:00:00'));
$timetable->setTimeEnd(new \DateTime('12:00:00'));
$em->persist($timetable);

$timetable = new \App\Entity\Timetable();
$timetable->setSection($bioSections[0]);
$timetable->setDay('Friday');
$timetable->setTimeStart(new \DateTime('09:00:00'));
$timetable->setTimeEnd(new \DateTime('12:00:00'));

$em->persist($timetable);

$timetable = new \App\Entity\Timetable();

$timetable->setSection($bioSections[1]);
$timetable->setDay('Tuesday');
$timetable->setTimeStart(new \DateTime('10:00:00'));
$timetable->setTimeEnd(new \DateTime('12:00:00'));
$em->persist($timetable);

$timetable = new \App\Entity\Timetable();

$timetable->setSection($bioSections[1]);
$timetable->setDay('Thursday');
$timetable->setTimeStart(new \DateTime('10:00:00'));
$timetable->setTimeEnd(new \DateTime('12:00:00'));


$em->persist($timetable);

$em->flush();


$teacher = $em->find(User::class, 4);
$section = $em->find(Section::class, 1);
$students = $section->getStudents();
$section->setTeacher($teacher);

$notificationBody = "The teacher for section: " . $section->getId() . " has been changed to: " . $teacher->getFirstName() . " " . $teacher->getLastName();
$em->persist($section);

foreach($students as $student) {

    $notification = new Notification();
    $appNotification = new AppNotification();
    $emailNotification = new EmailNotification();
    $notification->setUser($student);
    $appNotification->setBody($notificationBody);
    $appNotification->setStatus("new");
    $emailNotification->setBody($notificationBody);

    $notification->setAppNotification($appNotification);
    $notification->setEmailNotification($emailNotification);

    $em->persist($notification);
    $em->persist($appNotification);
    $em->persist($emailNotification);
}

$em->flush();

$notificationBody = "This is a notification to let you know you failed the course and need to leave uni.";

$notification = new Notification();
$appNotification = new AppNotification();
$emailNotification = new EmailNotification();
$notification->setUser($em->find(User::class, 5));
$appNotification->setBody($notificationBody);
$appNotification->setStatus("new");
$emailNotification->setBody($notificationBody);

$notification->setAppNotification($appNotification);
$notification->setEmailNotification($emailNotification);

$em->persist($notification);
$em->persist($appNotification);
$em->persist($emailNotification);

    $notifications = [
        'notif1',
        'notif2',
        'notif3',
        'notif4',
        'notif5',
        'notif6'
    ];

    foreach ($notifications as $notif) {

        $notification = new Notification();
        $appNotification = new AppNotification();
        $emailNotification = new EmailNotification();
        $notification->setUser($em->find(User::class, 5));
        $appNotification->setBody($notif);
        $appNotification->setStatus("new");
        $emailNotification->setBody($notif);

        $notification->setAppNotification($appNotification);
        $notification->setEmailNotification($emailNotification);

        $em->persist($notification);
        $em->persist($appNotification);
        $em->persist($emailNotification);
    }

$em->flush();

$notifications = $em->getRepository(Notification::class);

$arr = $notifications->createQueryBuilder("n")
    ->join("n.user", "u")
    ->where("u.id = :userId")
    ->setParameter(":userId", 5)
    ->getQuery()
    ->getResult();

$arr;
foreach($arr as $notif) {

    echo $notif->getAppNotification()->getBody() . " " . "at " . $notif->getTime()->format("H:i") . "\n";
}


$messages = [
    'Welcome to the course, I hope you are all ready to study 12 h a day and fail anyway.',
    'message 1',
    'message 2',
    'message 3'
];

foreach($messages as $message) {
    $announcement = new SectionAnnouncement();
    $announcement->setSection($section);
    $announcement->setAnnouncement($message);
    $em->persist($announcement);
}


$em->flush();

$annRepo = $em->find(Section::class, 1);
$array = $annRepo->getAnnouncements();

foreach($annRepo->getAnnouncements() as $message) {
    echo $message->getAnnouncement() . "\n";
}


$teacher2 = $em->find(User::class, 3);
$student3 = $em->find(User::class, 5);
$section2 = $em->find(Section::class, 2);
$student3->addSectionToStudent($section2);
$section2->setTeacher($teacher2);

$em->persist($section2);
$em->persist($student3);


$announcement1 = new SectionAnnouncement();
$announcement1->setSection($section2);
$announcement1->setAnnouncement('figure this out now.');
$em->persist($announcement1);

$em->flush();


$students = $section2->getStudents();
$students;













