<?php
require_once 'models/get.model.php';
class GetController
{
    //Obtener datos SIN filtro
    static function getData($table, $select, $orderBy, $orderMode, $starAt, $endAt)
    {

        $response = GetModel::getData($table, $select, $orderBy, $orderMode, $starAt, $endAt);

        $return = new GetController();
        $return->response($response);
    }
    //Peticion con filtro
    static function getDataFilter($table, $select, $linkTo, $equalTo, $orderBy, $orderMode, $starAt, $endAt)
    {

        $response = GetModel::getDataFilter($table, $select, $linkTo, $equalTo, $orderBy, $orderMode, $starAt, $endAt);


        $return = new GetController();
        $return->response($response);
    }

    //Obtener datos Sin filtro con relaciones
    static function getDataWithRelations($rel, $type, $type_id, $select, $orderBy, $orderMode, $starAt, $endAt)
    {

        $response = GetModel::getDataWithRelations($rel, $type, $type_id, $select, $orderBy, $orderMode, $starAt, $endAt);

        $return = new GetController();
        $return->response($response);
    }

    //Obtener datos con filtro con relaciones
    static function getDataWithRelationsFilter($rel, $type, $type_id, $select, $linkTo, $equalTo, $orderBy, $orderMode, $starAt, $endAt)
    {

        $response = GetModel::getDataWithRelationsFilter($rel, $type, $type_id, $select, $linkTo, $equalTo, $orderBy, $orderMode, $starAt, $endAt);

        $return = new GetController();
        $return->response($response);
    }
    //Obtener datos con filtro con relaciones


    //Peticion con buscador sin relaciones
    static function getDataSearch($table, $select, $linkTo, $search, $orderBy, $orderMode, $starAt, $endAt)
    {

        $response = GetModel::getDataSearch($table, $select, $linkTo, $search, $orderBy, $orderMode, $starAt, $endAt);

        $return = new GetController();
        $return->response($response);
    }
    //peticiones con buscador con relaciones
    static function getDataWithRelationsSearch($rel, $type, $type_id, $select, $linkTo, $equalTo, $orderBy, $orderMode, $starAt, $endAt)
    {

        $response = GetModel::getDataWithRelationsSearch($rel, $type, $type_id, $select, $linkTo, $equalTo, $orderBy, $orderMode, $starAt, $endAt);

        $return = new GetController();
        $return->response($response);
    }
    //Peticiones get para rangos
    static function getDataRange($filterTo, $inTo, $linkTo, $table, $between2, $between1, $select, $orderBy, $orderMode, $starAt, $endAt)
    {

        $response = GetModel::getDataRange($filterTo, $inTo, $linkTo, $table, $between2, $between1, $select, $orderBy, $orderMode, $starAt, $endAt);

        $return = new GetController();
        $return->response($response);
    }
    //Peticiones get para rangos
    static function getDataRangeWithRelations($filterTo, $inTo, $linkTo, $rel, $type, $type_id, $between2, $between1, $select, $orderBy, $orderMode, $starAt, $endAt)
    {

        $response = GetModel::getDataRangeWithRelations($filterTo, $inTo, $linkTo, $rel, $type, $type_id, $between2, $between1, $select, $orderBy, $orderMode, $starAt, $endAt);

        $return = new GetController();
        $return->response($response);
    }

    //Respuesta del controlador
    public function response($response)
    {

        if (!empty($response)) {

            $json = array(
                'status' => 200,
                'total' => count($response),
                'results' => $response

            );
        } else {

            $json = array(
                'status' => 404,
                'results' => 'error',
                'methot' => 'GET',
            );
        }
        echo json_encode($json, http_response_code($json['status']));
    }
}
