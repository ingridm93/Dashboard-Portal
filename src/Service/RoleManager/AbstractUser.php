<?php

namespace App\Service\RoleManager;

use App\Repository\CourseRepository;
use App\Repository\SectionRepository;
use App\Repository\SkillRepository;
use App\Repository\TimetableRepository;
use App\Repository\UserRepository;
use App\Service\CourseManager;
use App\Service\FlashMessage;
use App\Service\UserProvider;
use App\Controller\AbstractController;

abstract class AbstractUser
{

    /** @var CourseManager */

    public $courseManager;

    /** @var UserProvider */
    protected $userProvider;

    protected $user;
    /** @var FlashMessage  */
    protected $flashMessage;

    /** @var CourseRepository */
    protected $courseRepository;
    /** @var SkillRepository  */
    protected $skillRepository;

    /** @var TimetableRepository  */
    protected $timetableRepository;

    /** @var SkillRepository  */
    protected $userRepository;
    protected $sectionRepository;


    public function __construct(
        CourseManager $courseManager,
        UserProvider $userProvider,
        FlashMessage $flashMessage,
        CourseRepository $courseRepository,
        SkillRepository $skillRepository,
        UserRepository $userRepository,
        TimetableRepository $timetableRepository,
        SectionRepository $sectionRepository
    )
    {
        $this->courseManager = $courseManager;

        $this->userProvider = $userProvider;
        $this->user = $this->userProvider->getCurrentUser();

        $this->flashMessage = $flashMessage;
        $this->courseRepository = $courseRepository;
        $this->skillRepository = $skillRepository;
        $this->userRepository = $userRepository;
        $this->timetableRepository = $timetableRepository;
        $this->sectionRepository = $sectionRepository;

    }

    protected function sendResponse($data)
    {

        $response = new AbstractController();

        return $response->ajax(json_encode($data));
    }

    protected function getInputData($inputNameRequest, array $inputName)
    {

        $inputData = array_intersect_key(
            $inputNameRequest,
            array_fill_keys($inputName, "")
        );

        return $inputData;
    }

}