<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');

require_once "Database.php";
require_once "Router.php";
require_once "functions.php";
require_once "Task.php";
require_once "Employee.php";
require_once "Department.php";
require_once "Validator.php";

//Validator::validateDatabase();

$router = new Router();

$router->name('task')->uri('task')->action('Task@get')->register();
$router->name('task-add')->uri('task-add')->action('Task@post')->register();
$router->name('task-update-{0}')->uri('task-update')->action('Task@update')->register();
$router->name('task-delete-{0}')->uri('task-delete')->action('Task@delete')->register();

$router->name('employee')->uri('employee')->action('Employee@get')->register();
$router->name('employee-add')->uri('employee-add')->action('Employee@post')->register();
$router->name('employee-update-{0}')->uri('employee-update')->action('Employee@update')->register();
$router->name('employee-delete-{0}')->uri('employee-delete')->action('Employee@delete')->register();

$router->name('department')->uri('department-delete')->action('Department@get')->register();
$router->name('department-add')->uri('department-add')->action('Department@post')->register();
$router->name('department-update-{0}')->uri('department-update')->action('Department@update')->register();
$router->name('department-delete-{0}')->uri('department-delete')->action('Department@delete')->register();

$router->getCurrentRoute();
