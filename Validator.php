<?php
class Validator
{
    public static function validateDatabase()
    {
        $d = new Database();

        $result = $d->runQuery('SELECT `isApiCalled` FROM `web2_api` where `isApiCalled` = 0')->getResult();

        if(!empty($result))
        {
            self::fetch($d);
        }
    }

    private static function fetch(Database $db)
    {
        $d = [];
        $d[] = json_decode(file_get_contents('http://i875395.hera.fhict.nl/api/404285/department'), true);
        $d[] = json_decode(file_get_contents('http://i875395.hera.fhict.nl/api/404285/employee'), true);
        $d[] = json_decode(file_get_contents('http://i875395.hera.fhict.nl/api/404285/task'), true);

        self::insertDepartments($db, $d[0]);
        self::insertEmployees($db, $d[1]);
        self::insertTasks($db, $d[2]);

        $db->runQuery('UPDATE `web2_api` SET `isApiCalled`=1 WHERE `isApiCalled` = 0;');
    }

    private static function insertDepartments(Database $db, array $dataset)
    {
        foreach($dataset as $instance)
        {
            $db->runQuery("INSERT INTO `department` (`id`, `name`, `building`) VALUES (:id, :name, :building);", [':id' => "$instance[id]", ':name' => "$instance[name]", ':building' => "$instance[building]"]);

            foreach($instance['employees'] as $number){
                $db->runQuery('INSERT INTO `department_employee` (`department_id`, `employee_id`) VALUES (:department, :employee);', [':department' => $instance['id'], ':employee' => $number]);
            }
        }
    }

    private static function insertEmployees(Database $db, array $dataset)
    {
        foreach($dataset as $instance)
        {
            $db->runQuery("INSERT INTO `employee` (`id`, `first_name`, `last_name`, `birth_date`) VALUES (:id, :first, :last, :birth);", [':id' => $instance['id'], ':first' => $instance['first_name'], ':last' => $instance['last_name'], 'birth' => $instance['birth_date']]);
        }
    }

    private static function insertTasks(Database $db, array $dataset)
    {
        foreach($dataset as $instance)
        {
            if(!empty($instance['employees']))
                foreach($instance['employees'] as $id)
                $db->runQuery("INSERT INTO `employee_task` (`employee_id`, `task_id`) VALUES (:first, :last);", [':first' => $id, ':last' => $instance['id']]);

            $db->runQuery("INSERT INTO `task` (`id`, `name`) VALUE (:id, :first);", [':id' => $instance['id'], ':first' => $instance['name']]);
        }
    }
}
