<?php
require_once 'conexion.php';
require_once 'get.model.php';
class PutModel
{
    //Crear datos
    public static function PutData($table, $data, $id, $idname)
    {
        //validar id
        $response = GetModel::getDataFilter($table, $idname, $idname, $id, null, null, null, null);

        if (empty($response)) {
            $response = array(
                'commet ' => 'No existe el id en la bd'
            );
            return $response;
        }

        $set = "";

        foreach ($data as $key => $value) {
            $set .= $key . "=:" . $key . ",";
        }

        $set = substr($set, 0, -1); //elimina la ultima coma
        $db = Conexion::conexion();
        $instruccion = "UPDATE $table SET $set WHERE $idname = :$idname";
        $resultado = $db->prepare($instruccion);
        foreach ($data as $key => $value) {
            $resultado->bindParam(":" . $key, $data[$key], PDO::PARAM_STR);
        }
        $resultado->bindParam(":" . $idname, $id, PDO::PARAM_STR);

        if ($resultado->execute()) {
            $response = array(
                'commet' => 'El proceso se realizo correctamente'
            );
            return $response;
        } else {
            return $db->errorInfo();
        }
    }
}
