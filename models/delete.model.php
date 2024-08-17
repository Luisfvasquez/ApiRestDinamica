<?php
require_once 'conexion.php';
require_once 'get.model.php';
class deleteModel
{
    public static function deleteData($table, $id, $idname)
    {
        //validar id

        //validar id
        $response = GetModel::getDataFilter($table, $idname, $idname, $id, null, null, null, null);

        if (empty($response)) {
            $response = array(
                'commet ' => 'No existe el id en la bd'
            );
            return $response;
        }

        $db = Conexion::conexion();
        //Se eliminan los registros
        $instruccion = ("DELETE FROM $table WHERE $idname = :$idname");

        $resultado = $db->prepare($instruccion);
        $resultado->bindParam(':' . $idname, $id, PDO::PARAM_STR);
        if ($resultado->execute()) {
            $response = array(
                'commet ' => 'Datos Eliminados correctamente'
            );
            return $response;
        } else {
            return $db->errorInfo();
        }
    }
}
