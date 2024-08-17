<?php
require_once 'models/put.model.php';
class PutController
{
    //Peticiones put para editar datos

    public static function putData($table, $data, $id, $idname)
    {
        $response = PutModel::PutData($table, $data, $id, $idname);
        $return = new PutController();
        $return->response($response);
    }
    public function response($response)
    {

        if (!empty($response)) {

            $json = array(
                'status' => 200,
                'results' => $response,

            );
        } else {

            $json = array(
                'status' => 404,
                'results' => 'error',
                'methot' => 'PUT'
            );
        }
        echo json_encode($json, http_response_code($json['status']));
    }
}
