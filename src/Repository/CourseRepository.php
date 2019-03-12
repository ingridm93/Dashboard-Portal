<?php

namespace App\Repository;

use App\Entity\Course;
use App\Service\HelperFunctions;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;

class CourseRepository extends EntityRepository
{
    public function getAllCourses()
    {

        /** @var Course $course */

        $course = $this->findAll();

        return $course;
    }

    public function getCourseList($courseName)
    {
        $courses = $this->findBy(['courseName' => $courseName]);

        return $courses;
    }

    public function checkIfCourseExists($courseName, $courseLevel, $courseCode, $duration, $session, $status)
    {
        $course = $this->findBy([
            'courseName' => $courseName,
            'courseLevel' => $courseLevel,
            'courseCode' => $courseCode,
            'duration' => $duration,
            'session' => $session,
            'status' => $status
        ]);

        return $course ? false : true;
    }


    public function getCourseData($courseName)
    {
        $row = $this->createQueryBuilder("c")
            ->select("partial c.{id,courseName,courseCode,courseLevel,duration,session,status}")
            ->leftJoin('c.sections', "s")
            ->leftJoin("s.timetable", "t")
            ->where("c.courseName = :courseName")
            ->setParameter(":courseName", $courseName)
            ->getQuery()
            ->getResult();

        return $row;
    }

    public function getCourseRowCount(array $queryCases, array $courseFilter)
    {

        $queryCondition = $this->filterCourseOptions($queryCases, $courseFilter);

         $queryBuilder = $this->createQueryBuilder("c")
             ->select("count(c.id)")
            ->where($queryCondition['query']);


        foreach ($queryCondition['params'] as $k => $v) {

            $queryBuilder->setParameter($k, $v);
        }


        return $queryBuilder->getQuery()
            ->getArrayResult();

    }

    public function getAllCoursesByFilter($currentPage, $queryCases, $courseFilter)
    {
        $offset = ($currentPage - 1) * 5;

        $queryCondition = $this->filterCourseOptions($queryCases, $courseFilter);

        $queryBuilder = $this->createQueryBuilder("c")
            ->select("c, s")
            ->join("c.sections", "s")
            ->where($queryCondition['query'])
            ->groupBy("c.id, s.id")
            ->orderBy("c.courseName", "ASC")
            ->addOrderBy("c.courseCode", "ASC")
            ->setFirstResult($offset)
            ->setMaxResults(5);

        foreach ($queryCondition['params'] as $k => $v) {

            $queryBuilder->setParameter($k, $v);
        }

       return  $queryBuilder->getQuery()
            ->getResult();
        }

    public function filterCourseOptions($filterFields, $data)
    {
        $queryCondition = "";
        $params = [];

        foreach ($data as $filter => $item) {

            if (!empty($item)) {

                if(in_array($filter, $filterFields)) {

                    if ($queryCondition !== "") {
                        $queryCondition .= " AND ";
                        $queryCondition .=  "c." . $filter . " = :" . $filter;
                        $params[":" . $filter] = $item;
                    } else {
                        $queryCondition .=  "c." . $filter . " = :" . $filter;
                        $params[":" . $filter] = $item;
                    }
                }
            }
        }
        return ['query' => $queryCondition, 'params' => $params];
    }

}

