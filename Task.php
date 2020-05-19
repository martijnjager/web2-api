<?php
class Task
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function get()
    {
        $tasks = $this->db->runQuery('select id, name, description, due_date from task;')->getResult();
        $emps = $this->db->runQuery('SELECT * FROM employee_task WHERE task_id in ( SELECT id FROM Task );')->getResult();

        for($i = 0; $i < count($tasks); $i++){
            foreach($emps as $e)
            {
                $tasks[$i]['id'] = (int)$tasks[$i]['id'];
                $tasks[$i]['department_id'] = (int)$e['department_id'];

                if($e['task_id'] == $tasks[$i]['id'])
                {
                    $tasks[$i]['employees'][] = (int)$e['employee_id'];
                }
            }

        }

        return $tasks;
    }

    public function post($object)
    {
        if(!empty($object))
        {
            if(isset($object['description']))
                $this->db->runQuery("INSERT INTO `task` (`name`, description, due_date) values (:first, :desc, :d);",
                  [':first' => $object['name'], ':desc' => $object['description'], ':d' => $object['due_date']]);
            else
                $this->db->runQuery("INSERT INTO `task` (`name`, due_date) values (:first, :d);",
                  [':first' => $object['name'], ':d' => $object['due_date']]);

            if(isset($object['department_id']) && isset($object['employee_id'])) {
              $id = $this->db->runQuery('select max(id) from task;')->getResult();
              $this->db->runQuery('INSERT INTO `employee_task`(`employee_id`, `task_id`, `department_id`) VALUES (":e",":t",":d")',
                [':e' => $object['employee_id'], ':t' => $id, ':d' => $object['department_id']]);
            }
        }

        return $this->db->runQuery('select max(id) from task;')->getResult();
    }

  public function update($id, $object)
  {
    if(!empty($object))
    {
      if(isset($object['description']))
        $this->db->runQuery("update `task` set name = :first, description = :desc, due_date = :d where id = :id;",
          [':first' => $object['name'], ':desc' => $object['description'], ':d' => $object['due_date'], ':id' => $id]);
      else
        $this->db->runQuery("update `task` set name = :first, due_date = :d where id = :id;",
          [':first' => $object['name'], ':d' => $object['due_date'], ':id' => $id]);

      if(isset($object['employee_id']))
        $this->db->runQuery('UPDATE `employee_task` SET `employee_id`=:e where task_id = :t;',
          [':t' => $id, ':e' => $object['employee_id']]);

      if(isset($object['department_id']))
        $this->db->runQuery('UPDATE `employee_task` SET `department_id`=:d where task_id = :t;',
          [':d' => $object['department_id'], ':t' => $id]);
    }

    return $id;
  }

  public function delete($id)
  {
      $this->db->runQuery('DELETE FROM `task` WHERE id = :i', [':i' => $id]);
      $this->db->runQuery('DELETE FROM `employee_task` WHERE task_id = :i', [':i' => $id]);

      return $this->get();
  }
}
