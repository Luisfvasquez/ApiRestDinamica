<?php
require_once 'models/conexion.php';
require_once 'controllers/post.controller.php';
if (isset($_POST)) {

    $columns = array();

    foreach (array_keys($_POST) as $key => $value) {
        array_push($columns, $value);
    }

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

    $response = new PostController();

    //Registros de usuario
    if (isset($_GET["register"]) && $_GET["register"] == true) {
        $suffix = $_GET['suffix'] ?? 'usuario';

        $response->posRegister($table, $_POST, $suffix);
    } else if (isset($_GET["login"]) && $_GET["login"] == true) { //Login de usuario
        $suffix = $_GET['suffix'] ?? 'usuario';

        $response->posLogin($table, $_POST, $suffix);
    } else {

       

        if (isset($_GET["token"])) {

            if ($_GET['token'] == "no" && isset($_GET['except'])) {

                 //Validar post para usuarios no autorizados

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
                
                $response->posData($table, $_POST);

            } else {

                 //Validar post para usuarios autorizados

                $tableToken = $_GET['table'] ?? null;
                $suffix = $_GET['suffix'] ?? null;

                $validate = Conexion::tokenValidate($_GET['token'], $tableToken, $suffix);
                

                if ($validate == "ok") {

                    $response->posData($table, $_POST);
               
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
}
