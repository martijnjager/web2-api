<?php

class Employee
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function get()
    {
        $tasks = $this->db->runQuery('select id, department_id, first_name, last_name, birth_date from employee join department_employee on employee_id = id;')->getResult();

        for ($i = 0; $i < count($tasks); $i++)
            $tasks[$i]['id'] = (int)$tasks[$i]['id'];

        return $tasks;
    }
}
