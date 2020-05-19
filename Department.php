<?php

class Department
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function get()
    {
        $tasks = $this->db->runQuery('select id, name, building from department;')->getResult();
        $emps = $this->db->runQuery('SELECT employee_id, department_id FROM department_employee;')->getResult();

        for($i = 0; $i < count($tasks); $i++){
            foreach($emps as $e)
            {
                $tasks[$i]['id'] = (int)$tasks[$i]['id'];
                if($e['department_id'] == $tasks[$i]['id'])
                {
                    $tasks[$i]['employees'][] = (int)$e['employee_id'];
                }
            }

        }

        return $tasks;
    }
}
