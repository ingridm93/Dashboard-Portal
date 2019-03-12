<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Notification
 * @ORM\Entity(repositoryClass= "App\Repository\NotificationRepository")
 * @ORM\Table(name="notification")
 */

class Notification
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="id")
     **/

    private $user;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\AppNotification", mappedBy="notification")
     */
    private $appNotification;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\EmailNotification", mappedBy="notification")
     */
    private $emailNotification;

    /**
     * @ORM\Column(name="timestamp", type="datetime")
     */
    private $time;


    public function __construct()
    {
        $this->setTime(new \DateTime('now'));
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getAppNotification()
    {
        return $this->appNotification;
    }

    /**
     * @param mixed $appNotification
     */
    public function setAppNotification(AppNotification $appNotification)
    {
        $appNotification->setNotification($this);
        $this->appNotification = $appNotification;
    }

    /**
     * @return mixed
     */
    public function getEmailNotification()
    {
        return $this->emailNotification;
    }

    /**
     * @param mixed $emailNotification
     */
    public function setEmailNotification(EmailNotification $emailNotification)
    {
        $emailNotification->setNotification($this);
        $this->emailNotification = $emailNotification;
    }

    public function setUser(User $user) {

        $this->user = $user;
    }

    public function getUser() {

        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }


}
