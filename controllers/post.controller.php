<?php
require_once 'models/post.model.php';
require_once 'models/get.model.php';
require_once 'models/put.model.php';
require_once 'models/conexion.php';
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;

class PostController
{
    //Peticiones post para crear datos

    public static function posData($table, $columns)
    {
        $response = PostModel::PosData($table, $columns);
        $return = new PostController();
        $return->response($response, null, null);
    }


    //Para registrar usuarios
    public static function posRegister($table, $data, $suffix)
    {

        if (isset($data['password_client']) && $data['password_client'] != null) {
            $crypt = crypt($data['password_client'], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
            $data['password_client'] = $crypt;

            $response = PostModel::PosData($table, $data);
            $return = new PostController();
            $return->response($response, null, $suffix);
        } else {

            //Aplicaciones externas

            $response = PostModel::posData($table, $data);

            if (isset($response['commet']) && $response['commet'] == 'El proceso se realizo correctamente') {
                //Validar que el usuario exista en la BD
                $response = GetModel::getDataFilter($table, "*", 'correo', $data["correo"], null, null, null, null);

                if (!empty($response)) {

                    $token = Conexion::JWT($response[0]->id, $response[0]->correo);

                    $jwt = JWT::encode($token, 'fasdfds5f4asd3f', 'HS256');

                    //Actualizar el token en la BD
                    $data = array(
                        "token_client" => $jwt,
                        "token_exp_client" => $token["exp"]
                    );

                    $update = PutModel::PutData($table, $data, $response[0]->id, 'id');

                    if (isset($update['commet']) && $update['commet'] == 'El proceso se realizo correctamente') {

                        $response[0]->token_client = $jwt;
                        $response[0]->token_exp_client = $token["exp"];

                        $return = new PostController();
                        $return->response($response, null, $suffix);
                    }
                }
            }
        }
    }


    //Para login usuarios
    public static function posLogin($table, $data, $suffix)
    {
        //Validar que el usuario exista en la BD
        $response = GetModel::getDataFilter($table, "*", 'correo', $data["correo"], null, null, null, null);

        if (!empty($response)) {

            if ($response[0]->password_client != null) {

                //Se encripta la contraseña para compararla con la de la BD
                $crypt = crypt($data['password_client'], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');


                if ($response[0]->password_client == $crypt) {

                    $token = Conexion::JWT($response[0]->id, $response[0]->correo);

                    $jwt = JWT::encode($token, 'fasdfds5f4asd3f', 'HS256');

                    //Actualizar el token en la BD
                    $data = array(
                        "token_client" => $jwt,
                        "token_exp_client" => $token["exp"]
                    );

                    $update = PutModel::PutData($table, $data, $response[0]->id, 'id');

                    if (isset($update['commet']) && $update['commet'] == 'El proceso se realizo correctamente') {

                        $response[0]->token_client = $jwt;
                        $response[0]->token_exp_client = $token["exp"];
                        $return = new PostController();
                        $return->response($response, null, $suffix);
                    }
                } else {
                    $response = null;
                    $return = new PostController();
                    $return->response($response, "Contraseña incorrecta", $suffix);
                }
            } else {
                $token = Conexion::JWT($response[0]->id, $response[0]->correo);

                $jwt = JWT::encode($token, 'fasdfds5f4asd3f', 'HS256');

                //Actualizar el token en la BD para usuarios registrados con aplicaciones externas
                $data = array(
                    "token_client" => $jwt,
                    "token_exp_client" => $token["exp"]
                );

                $update = PutModel::PutData($table, $data, $response[0]->id, 'id');

                if (isset($update['commet']) && $update['commet'] == 'El proceso se realizo correctamente') {

                    $response[0]->token_client = $jwt;
                    $response[0]->token_exp_client = $token["exp"];
                    $return = new PostController();
                    $return->response($response, null, $suffix);
                }
            }
        } else {
            $response = null;
            $return = new PostController();
            $return->response($response, "No se encontro el usuario", $suffix);
        }
    }


    public function response($response, $error, $suffix)
    {

        if (!empty($response)) {

            //Quitar la contraseña de la respuesta
            if (isset($response[0]->password_client)) {
                unset($response[0]->password_client);
            }
            $json = array(
                'status' => 200,
                'results' => $response,

            );
        } else {
            if ($error != null) {
                $json = array(
                    'status' => 400,
                    'results' => $error,
                    'methot' => 'POST'
                );
            } else {
                $json = array(
                    'status' => 404,
                    'results' => 'Not found',
                    'methot' => 'POST'
                );
            }
        }
        echo json_encode($json, http_response_code($json['status']));
    }
}
