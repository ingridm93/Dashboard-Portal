<?php

namespace App\Service;

use PDO;

class AdminCourseManager
{

    private $pdo;
    private $helper;

    public function __construct(PDO $pdo, HelperFunctions $helper)
    {
        $this->pdo = $pdo;
        $this->helper = $helper;
    }

    public function getCourseRowCount(array $queryCases, array $courseFilter)
    {

        $queryCondition = $this->helper->filterCourseOptions($queryCases, $courseFilter);

        $query = "SELECT COUNT(*) FROM course" . $queryCondition['query'];
        $result = $this->pdo->prepare($query);
        foreach ($queryCondition['params'] as $k => &$v) {

            $result->bindParam($k, $v);

        }
        $result->execute();
        $count = $result->fetchColumn();
        return $count ? $count : 0;
    }




    public function getSectionInformation($courseId)
    {

        $query = "SELECT section.id AS section_id, user_id, first_name, last_name FROM section JOIN user_section ON section.id = user_section.section_id JOIN user ON user_section.user_id = user.id WHERE section.course_id = :courseId AND user.role = 'teacher'";
        $result = $this->pdo->prepare($query);
        $result->bindParam(':courseId', $courseId, PDO::PARAM_INT);

        $result->execute();

        $rows = $result->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    public function getTimeTablePerSection($sectionId)
    {

        $query = "SELECT * FROM section_timetable WHERE section_id = :sectionId";
        $result = $this->pdo->prepare($query);

        $result->bindParam(':sectionId', $sectionId, PDO::PARAM_INT);
        $result->execute();
        $rows = $result->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    public function filterByCourse($queryCondition = "", $param, $column)
    {

        $query = "SELECT * FROM course " . $queryCondition;
        $result = $this->pdo->prepare($query);

        $result->bindParam($param, $column);
        $result->execute();
        $rows = $result->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    public function getTeachersPerSkill ($skill) {

        $query = "SELECT teacher_id, first_name, last_name FROM specialization JOIN user ON user.id = teacher_id WHERE skill = :skill";
        $result = $this->pdo->prepare($query);

        $result->bindParam(":skill", $skill, PDO::PARAM_STR);
        $result->execute();

        $rows = $result->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    public function getStudentsPerSection ($sectionId, $courseId) {

        $query = "SELECT user_id, first_name, last_name FROM user_section JOIN user ON user_id = user.id WHERE section_id = :sectionId AND course_id = :courseId AND role = 'student'";
        $result = $this->pdo->prepare($query);

        $result->bindParam(":sectionId", $sectionId, PDO::PARAM_INT);
        $result->bindParam(":courseId", $courseId, PDO::PARAM_INT);
        $result->execute();

        $rows = $result->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    public function getAllStudents($sectionId) {

        $query =  "SELECT id, first_name, last_name FROM user WHERE id NOT IN (SELECT user_id FROM user_section JOIN user ON user.id = user_section.user_id WHERE section_id = :sectionId AND role = 'student') AND role = 'student'";
        $result = $this->pdo->prepare($query);
        $result->bindParam(":sectionId", $sectionId, PDO::PARAM_STR);
        $result->execute();

        $rows = $result->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    public function deleteSection($sectionId) {
        $query = "DELETE FROM section WHERE id = :sectionId";
        $result = $this->pdo->prepare($query);
        $result->bindParam(":sectionId", $sectionId, PDO::PARAM_STR);
        return $result->execute();
    }


    public function deleteTimetablePerSection($sectionId)
    {

        $query = "DELETE FROM section_timetable WHERE section_id = :sectionId";
        $result = $this->pdo->prepare($query);
        $result->bindParam(":sectionId", $sectionId, PDO::PARAM_INT);
        return $result->execute();
    }

    public function addStudentToUserSection ($studentId, $courseId, $sectionId) {
        $query = "INSERT INTO user_section (user_id, course_id, section_id) VALUES (:studentId, :courseId, :sectionId)";
        $result = $this->pdo->prepare($query);
        $result->bindParam(':studentId', $studentId, PDO::PARAM_STR);
        $result->bindParam(':courseId', $courseId, PDO::PARAM_STR);
        $result->bindParam(':sectionId', $sectionId, PDO::PARAM_STR);
        return $result->execute();
    }

    public function deleteStudentFromSection($studentId, $courseId, $sectionId) {

        $query = "DELETE FROM user_section WHERE user_id = :studentId AND section_id = :sectionId AND course_id = :courseId";
        $result = $this->pdo->prepare($query);
        $result->bindParam(':studentId', $studentId, PDO::PARAM_STR);
        $result->bindParam(':courseId', $courseId, PDO::PARAM_STR);
        $result->bindParam(':sectionId', $sectionId, PDO::PARAM_STR);
        return $result->execute();
    }
}