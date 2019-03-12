<?php

namespace App\Service\RoleManager;

class TeacherManager extends AbstractUser
{

    public function addTeacherTimetable($inputData)
    {
        if ($inputData) {

            $timetableInputData = $this->getInputData($inputData, ['weekdays', 'timeStart', 'timeEnd', 'sectionId']);

            $weekdays = $timetableInputData['weekdays'];
            $timeStart = $timetableInputData['timeStart'];
            $timeEnd = $timetableInputData['timeEnd'];
            $sectionId = $timetableInputData['sectionId'];

            if ($this->timetableInputDataIsValid($inputData)) {

                for ($i = 0; $i < count($weekdays); $i++) {

                    $this->checkForConflictAndCreateTimetable($this->user, $weekdays[$i], $timeStart[$i], $timeEnd[$i], $sectionId, $timeslotId=NULL, "create");
                }
            }
            return;
        }
    }


    protected function timetableInputDataIsValid($inputData)
    {

        $timetableInputData = $this->getInputData($inputData, ['weekdays', 'timeStart', 'timeEnd', 'sectionId']);

        return count($timetableInputData['weekdays']) === count($timetableInputData['timeStart']) && count($timetableInputData['weekdays']) === count($timetableInputData['timeEnd']);
    }

    public function checkForConflictAndCreateTimetable($user , $weekday, $timeStart, $timeEnd, $sectionId, $timeslotId, $queryType)
    {

        if ($rows = $this->timetableRepository->checkTeacherSchedule($user, $weekday)) {

            return $this->checkForConflict($sectionId, $timeslotId, $weekday, $timeStart, $timeEnd, $rows, $queryType);

        } else {
            return $this->determineQuery($queryType, $sectionId, $weekday, $timeStart, $timeEnd, $timeslotId);
        }
    }

    protected function checkForConflict($sectionId, $timeslotId, $weekday, $timeStart, $timeEnd, array $rows, $queryType)
    {

        foreach ($rows as $row) {

            $dbStart = str_replace(":", "", $row->getTimeStart()->format("H:i:s"));
            $dbEnd = str_replace(":", "", $row->getTimeEnd()->format("H:i:s"));


            if ($timeStart < $dbStart && $timeEnd <= $dbStart) {

                return $this->determineQuery($queryType, $sectionId, $weekday, $timeStart, $timeEnd, $timeslotId);

            } else if ($timeStart >= $dbEnd && $timeEnd >= $dbEnd) {

                return $this->determineQuery($queryType, $sectionId, $weekday, $timeStart, $timeEnd, $timeslotId);

            } else {
                $this->courseManager->deleteSectionTimetable($sectionId);
                if (isset($_SESSION['flashMessages']['success'])) {
                    unset($_SESSION['flashMessages']['success']);
                }

                $this->flashMessage->add('danger', 'timetable conflict on: ' . $weekday . " at " . $timeStart . "-" . $timeEnd);
                return;
            }
        }
        $this->flashMessage->add('success', 'you have successfully added this course to your schedule');
    }

    protected function determineQuery($queryType, $sectionId, $weekday, $timeStart, $timeEnd, $timeslotId)
    {

        if ($queryType === "create") {


            return $this->courseManager->createSectionTimetable($sectionId, $weekday, $timeStart, $timeEnd);

        } else if ($queryType === "update") {

            return $this->courseManager->updateTimeslot($timeslotId, $weekday, $timeStart, $timeEnd);
        }
    }

}