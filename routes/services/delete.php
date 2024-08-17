<?php
require_once("models/conexion.php");
require_once("controllers/delete.controller.php");

if (isset($_GET["id"]) && isset($_GET["idname"])) {

    $columns = array($_GET["idname"]);;

    if (empty(Conexion::getColumnData($table, $columns))) {
        $json = array(
            'status' => 404,
            'results' => 'error',
            'message' => 'No se encontro la tabla o columna'

        );
        echo json_encode($json, http_response_code($json['status']));
        return;
    }

    //Peticion PUT para usuarios autorizados
    if (isset($_GET["token"])) {
        $tableToken = $_GET['table'] ?? null;
        $suffix = $_GET['suffix'] ?? null;

        $validate = Conexion::tokenValidate($_GET['token'], $tableToken, $suffix);


        if ($validate == "ok") {


            $response = new DeleteController();
            $response->deleteData($table, $_GET['id'], $_GET['idname']);
        }

        if ($validate == "expired") {
            $json = array(
                'status' => 303,
                'results' => 'Error: The token has expired'
            );
            echo json_encode($json, http_response_code($json['status']));
            return;
        }

        if ($validate == "no-auth") {
            $json = array(
                'status' => 400,
                'results' => 'Error: The user is not authorized'
            );
            echo json_encode($json, http_response_code($json['status']));
            return;
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
