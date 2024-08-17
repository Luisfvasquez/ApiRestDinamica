<?php
require_once("models/conexion.php");
require_once("controllers/put.controller.php");

if (isset($_GET["id"]) && isset($_GET["idname"])) {

    $data = array();
    parse_str(file_get_contents('php://input'), $data);

    $columns = array();
    foreach (array_keys($data) as $key => $value) {
        array_push($columns, $value);
    }
    array_push($columns, $_GET['idname']);
    $columns = array_unique($columns);

    //validar la tabla y las columnas
    if (empty(Conexion::getColumnData($table, $columns))) {
        $json = array(
            'status' => 404,
            'results' => 'error',
            'message' => 'No se encontro la tabla o columna'

        );
        echo json_encode($json, http_response_code($json['status']));
        return;
    }
    
    
    if (isset($_GET["token"])) {
        //Peticion PUT para no usuarios autorizados
        if ($_GET['token'] == "no" && isset($_GET['except'])) {

            $columns = array($_GET['except']);
            //validar la tabla y las columnas
            if (empty(Conexion::getColumnData($table, $columns))) {
                $json = array(
                    'status' => 404,
                    'results' => 'error',
                    'message' => 'No se encontro la tabla o columna'

                );
                echo json_encode($json, http_response_code($json['status']));
                return;
            }
            
                $responde = new PutController();
                $responde->putData($table, $data, $_GET["id"], $_GET["idname"]);
        
        } else {
            //Peticion PUT para usuarios autorizados
            $tableToken =$_GET['table'] ?? null;
            $suffix =$_GET['suffix'] ?? null;

            $validate = Conexion::tokenValidate($_GET['token'],$tableToken,$suffix);
        
        
            if($validate=="ok"){

                $responde = new PutController();
                $responde->putData($table, $data, $_GET["id"], $_GET["idname"]);
        
        } 
        
        if($validate=="expired"){
            $json=array(
                'status'=>303,
                'results'=>'Error: The token has expired'
            );
            echo json_encode($json,http_response_code($json['status']));
            return ;
        }

        if($validate=="no-auth"){
            $json=array(
                'status'=>400,
                'results'=>'Error: The user is not authorized'
            );
            echo json_encode($json,http_response_code($json['status']));
            return ;


        }
    }
    } else {

        $json = array(
            'status' => 400,
            'results' => 'Error: The token is required'

        );
        echo json_encode($json, http_response_code($json['status']));
        return;
    }
}
