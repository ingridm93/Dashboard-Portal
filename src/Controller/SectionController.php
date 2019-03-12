<?php

namespace App\Controller;

use App\Repository\SectionAnnouncementRepository;
use App\Repository\SectionRepository;
use App\Repository\UserRepository;
use App\Service\UserProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

class SectionController extends AbstractController
{


    private $userProvider;
    private $sectionRepository;
    private $router;
    private $announcementRepository;
    private $userRepository;

    public function __construct(Router $router, UserProvider $userProvider, SectionRepository $sectionRepository, SectionAnnouncementRepository $announcementRepository, UserRepository $userRepository)
    {

        $this->router = $router;
        $this->userProvider = $userProvider;
        $this->sectionRepository = $sectionRepository;
        $this->userRepository = $userRepository;
        $this->announcementRepository = $announcementRepository;
    }

    public function loadUserSections()
    {
        $user = $this->userProvider->getCurrentUser();

        if($user->getRole() === 'student') {
            $sections = $this->userRepository->find($user->getId())->getStudentSections();

        }

        if($user->getRole() === 'teacher') {
            $sections = $this->userRepository->find($user->getId())->getTeacherSections();

        }

        return $this->render('section-list.html.twig', ['sections' => $sections]);

    }

    public function viewSectionAnnouncements(Request $request)
    {
        $section = $this->sectionRepository->find($request->get("id"));

        return $this->render('section-announcement.html.twig',['section'=> $section]);
    }
}
