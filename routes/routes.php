<?php
require_once 'models/conexion.php';
require_once 'controllers/get.controller.php';
                                                        //Genera Array con la URL
$routesArray = explode('/', $_SERVER['REQUEST_URI']);   //Separar la URL por cada / que encuentre
$routesArray=array_filter($routesArray);                //Eliminar los espacios en blanco

if(count($routesArray)==0){
    $json= array(
        'status' => 404,
        'result' => 'error',
        'message' => 'No se encontro la ruta'
    );

    echo json_encode($json ,http_response_code(404));

    return;
}

//Si contiene algo la url verifica el proceso a tomar
if(count($routesArray) ==1 && isset($_SERVER['REQUEST_METHOD'])){
   
     $table = explode('?',$routesArray[1])[0]; 

        //Validar la clave secreta
        if(!isset((getallheaders()["Authorization"])) || ( getallheaders()["Authorization"]) !=Conexion::apiKey()){
            
            if(in_array($table,Conexion::publicAccess())==0){
                
                $json= array(
                    'status' => 404,
                    'result' => 'You are not authorized to make this request',
                );
                echo json_encode($json ,http_response_code($json['status']));
                return;

            }else{
                //Acceso publico
            $response =  new GetController();
            $response->getData($table,"*",null,null,null,null );  
                return;
            }
        }
        
        //Peticiones GET
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
             include 'services/get.php';
    }
    
     //Peticiones POST
     if($_SERVER['REQUEST_METHOD'] == 'POST'){
        include 'services/post.php';
    }

     //Peticiones PUT
     if($_SERVER['REQUEST_METHOD'] == 'PUT'){
        include 'services/put.php';
    }

     //Peticiones DELETE
     if($_SERVER['REQUEST_METHOD'] == "DELETE"){
     
       include 'services/delete.php';
      
    }
}


