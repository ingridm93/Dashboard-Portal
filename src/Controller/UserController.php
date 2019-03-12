<?php

namespace App\Controller;

use App\Repository\SkillRepository;
use App\Repository\UserRepository;
use App\Service\FlashMessage;
use App\Service\UserManager;
use App\Service\UserProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

class UserController extends AbstractController
{

    /** @var Router */
    private $router;

    /** @var UserProvider  */
    protected $userProvider;
    protected $user;

    /** @var FlashMessage  */
    protected $flashMessage;

    /** @var UserRepository  */
    private $userRepository;

    /** @var UserManager  */
    private $userManager;
    private $skillRepository;


    public function __construct(Router $router, UserProvider $userProvider, UserRepository $userRepository, UserManager $userManager, FlashMessage $flashMessage, SkillRepository $skillRepository)
    {
        $this->router = $router;
        $this->userProvider = $userProvider;
        $this->user = $this->userProvider->getCurrentUser();
        $this->flashMessage = $flashMessage;
        $this->userRepository = $userRepository;
        $this->userManager = $userManager;
        $this->skillRepository = $skillRepository;

    }

    public function register(Request $request)
    {

        $userInfo = $request->request->all();

        // @TODO is this $userInfo needed? if not there then the conditional statement will run?

        if ($userInfo) {

            if ($this->isValid($request)) {

                $this->createUser($request);

                $this->flashMessage->add('success', 'you have successfully registered');

                return new RedirectResponse($this->router->generate('dashboard'));

            }
        }

        return $this->render('index.html.twig');
    }

    protected function isValid(Request $request)
    {
        $userInfo = $this->userInfo($request);

        if(!$request->request->get('firstName') || !$request->request->get('lastName') || !$request->request->get('email') || !$request->request->get('username') || !$request->request->get('password')) {
            $this->flashMessage->add('danger', 'all fields must be filled. please try again');
        }

        return $userInfo['firstName'] && $userInfo['lastName'] && $userInfo['email'] && $userInfo['username'] && $userInfo['password'] && $this->validateEmailAndUsername($userInfo['email'], $userInfo['username']);

    }

    protected function userInfo(Request $request)
    {

        $userInfo = array_intersect_key(
            $request->request->all(),
            array_fill_keys(['firstName', 'lastName', 'email', 'username', 'password', 'skill'], "")
        );

        return $userInfo;
    }


    protected function validateEmailAndUsername($email, $username)
    {

        if (!$this->userRepository->validateUsername($username)) {
            $this->flashMessage->add('warning', 'the username is already in use');
        }

        if (!$this->userRepository->validateEmail($email)) {
            $this->flashMessage->add('warning', 'the email is already in use');

        }
        return $this->userRepository->validateUsername($username) && $this->userRepository->validateEmail($email);
    }

    public function createUser(Request $request)
    {
        $userInfo = $this->userInfo($request);
        $skills = $request->request->get('skill');

        // @TODO change this restriction to a hidden field with what roles are allowed/not..

        if ($skills && $skills !== []) {


            $teacher = $this->userManager->createUser($userInfo['firstName'], $userInfo['lastName'], $userInfo['email'], $userInfo['username'], $userInfo['password'], 'teacher');

                $this->userManager->createSkill($teacher, $skills);

        } else if ($request->request->get('role') === 'admin') {
            $this->userManager->createUser($userInfo['firstName'], $userInfo['lastName'], $userInfo['email'], $userInfo['username'], $userInfo['password'], 'admin');
        }
        else {

            $this->userManager->createUser($userInfo['firstName'], $userInfo['lastName'], $userInfo['email'], $userInfo['username'], $userInfo['password'], 'student');
        }
    }


    public function renderTeacherForm()
    {
        $courseList = $this->skillRepository->getAllSkills();
        return $this->render('teacher-register.html.twig', ['courseList' => $courseList]);
    }


    public function renderStudentForm()
    {
        return $this->render('student-register.html.twig');
    }

    public function renderAdminForm()
    {
        return $this->render('admin-register.html.twig');
    }


    public function login(Request $request)
    {
        if ($this->checkCredentials($request)) {

            $this->flashMessage->add('success', "you have successfully logged in");

            return new RedirectResponse($this->router->generate('dashboard'));

        }

        return $this->render('login.html.twig');
    }

    public function checkCredentials(Request $request)
    {

        if ($request->request->get('username') && $request->request->get('password')) {

            if(!$this->userRepository->readUser($request->request->get('username'), $request->request->get('password'))) {

                $this->flashMessage->add('danger', 'your email or password is incorrect');
            }
            return $this->userRepository->readUser($request->request->get('username'), $request->request->get('password'));
        }

    }

    public function logout()
    {
        session_destroy();

        return new RedirectResponse($this->router->generate('register'));
    }

    public function populateUser ()
    {
        $this->userRepository->fillDb();
        $this->userRepository->addSectionToCourse([
            'courseName' => 'BIO',
            'courseLevel' => 2,
            'courseCode' => 202,
            'duration' => 'H',
            'session' => 'F',
            'status' => 'Online'
        ]);
        $this->userRepository->addSectionToCourse([
            'courseName'=> 'CHM',
            'courseLevel'=> 3,
            'courseCode'=> 200,
            'duration'=> 'H',
            'session'=>'S',
            'status'=> 'Offline'
        ]);

        $userSkills = $this->userRepository->find(1)->getSkills();
    }
}
