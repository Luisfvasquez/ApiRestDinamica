<?php
//Errores

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'C:laragon/www/apis/error.log');

//CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: origin,X-Requested-With ,Content-Type, Accept");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Content-Type: application/json; charset=UTF-8");


//Requerimientos 

require_once 'controllers/routes.controller.php';

$index = new RoutesController();
$index->index();