<?php

namespace App\Controller;

use App\Service\Calendar;

class CalendarController extends AbstractController {

    public $calendar;
    public $arr;
    public $monthStartDay;
    private $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];




    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function calendar() {

        $now = (new \DateTime())->modify('first day of this month');
        $firstDayOfMonth =  (new \DateTime())->modify('first day of this month');
        $startInt =  intval($firstDayOfMonth->format("N")) - 1;
        $firstDayOfMonth->sub(new \DateInterval('P'. $startInt .'D'));

        $lastDayOfMonth =  (new \DateTime())->modify('last day of this month')->modify('+1 day');
        $endInt = 7 -intval( $lastDayOfMonth->format("N"));
        $lastDayOfMonth->add(new \DateInterval('P'. $endInt . 'D'));


        $datePeriod = new \DatePeriod(
            (new \DateTime())->modify($firstDayOfMonth->format("Y-m-d")),
            new \DateInterval('P1D'),
            (new \DateTime())->modify($lastDayOfMonth->format("Y-m-d"))->modify('+1 day')
        );

        $prev = $datePeriod->start->format("m");
        $current = $now->format("m");
        $next = $datePeriod->end->format("m");

        $arr=[];

        foreach($datePeriod as $date) {

            /** @var \DateTime $date */
            $week = $date->format('W');


            if($date->format("m") < $current || $date->format("m") > $current) {

                $arr[$week][]['blurred'] = (new \DateTime($date->format("Y-m-d")));

            } else {
                $arr[$week][]['active'] = (new \DateTime($date->format("Y-m-d")));

            }
        }
        return $this->render('calendar.html.twig', ['dayLabels' => $this->days, 'arr' => $arr]);
    }
}