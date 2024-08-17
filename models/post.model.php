<?php
require_once 'conexion.php';
class PostModel
{
    //Crear datos
    public static function PosData($table, $data)
    {

        $columns = "";
        $params = "";

        $db = Conexion::conexion();
        foreach ($data as $key => $value) {
            $columns .= $key . ",";
            $params .= ":" . $key . ",";
        }

        $columns = substr($columns, 0, -1);
        $params = substr($params, 0, -1);


        $instruccion = "INSERT INTO $table ($columns) VALUES ($params)";
        $resultado = $db->prepare($instruccion);

        foreach ($data as $key => $value) {
            $resultado->bindParam(":" . $key, $data[$key], PDO::PARAM_STR);
        }

        if ($resultado->execute()) {
            $response = array(
                'lastid' => $db->lastInsertId(),
                'commet' => 'El proceso se realizo correctamente'
            );
            return $response;
        } else {
            return $db->errorInfo();
        }
    }
}
