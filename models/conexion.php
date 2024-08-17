<?php
require_once "get.model.php";
class Conexion
{ //Ejecuta la canexion a la BBDD
    
    public static function apiKey(){
    
        return "Clave";
    
    }

    public static function publicAccess(){
        $table = ['posts'];
        return $table;
    }

    public static function conexion()
    {
        try {

            $conexion = new PDO("mysql:host=localhost; dbname=api.codersfree", "root", ""); //Datos de la BBDD
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //Controla las exepciones
            $conexion->exec("SET CHARACTER SET utf8");
        } catch (Exception $e) { //Muestra los errores en la conexion hacia la BBDD
            die("Error en BBDD " . $e->getMessage());
            echo "Error en linea " . $e->getLine();
        }

        return $conexion;
    }

    public static function getColumnData($table, $columns)
    {
        //Traer todas las columnas de una tabla
        $validate = Conexion::conexion()
            ->query("SELECT column_name AS item FROM information_schema.columns WHERE table_schema ='api.codersfree' AND table_name = '$table'")
            ->fetchAll(PDO::FETCH_OBJ);

        
        if (empty($validate)) { //Validamos la existencia de la tabla
            return null;
        } else {
            //Colunmas globales
            if ($columns[0] == '*') {
                array_shift($columns); //Elimina el primer indice de un arreglo
            }

            //Validar la existencia de las columnas especificas
            $sum = 0;
            foreach ($validate as $key => $value) {

                $sum += in_array($value->item, $columns);
            }

            return $sum == count($columns) ? $validate : null;
        }
    }
    //Token de autenticacion
    public static function JWT($id, $email)
    {
        $time = time();
        $token = array(
            "iat" => $time, //Tiempo de creacion
            "exp" => $time + (60 * 60 * 24), //Tiempo de expiracion 
            "data" => [
                "id" => $id,
                "email" => $email
            ]
        );

        return $token;
    }

    //Validacion de token de seguridad
    public static function tokenValidate($token,$tableToken,$suffix){
       $user=GetModel::getDataFilter($tableToken,"token_exp_".$suffix,"token_".$suffix,$token,null,null,null,null);
       if(!empty($user)){
           
        $time=time();
            if($time<$user[0]->{"token_exp_".$suffix}){
                return "ok";
            }else{
                return "expired";
            }
       }else{
              return "no-auth";
       }
    }

    
}
