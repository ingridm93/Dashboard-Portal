<?php
namespace App\Service;

class HelperFunctions {

    public $query;

    public function filterCourseOptions($filterFields, $data)
    {
        $queryCondition = "";
        $params = [];

        foreach ($data as $filter => $item) {

            if (!empty($item)) {

                if(in_array($filter, $filterFields)) {

                    if ($queryCondition !== "") {
                        $queryCondition .= " AND ";
                        $queryCondition .= $filter . " = :" . $filter;
                        $params[":" . $filter] = $item;
                    } else {
                        $queryCondition .= " WHERE ";
                        $queryCondition .= $filter . " = :" . $filter;
                        $params[":" . $filter] = $item;
                    }
                }
            }
        }
        return ['query' => $queryCondition, 'params' => $params];
    }
}