<?php
require_once 'models/delete.model.php';
class DeleteController
{

    public static function deleteData($table, $id, $idname)
    {
        $response = deleteModel::deleteData($table, $id, $idname);
        $return = new DeleteController();
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
                'methot' => 'Delete'
            );
        }
        echo json_encode($json, http_response_code($json['status']));
    }
}
