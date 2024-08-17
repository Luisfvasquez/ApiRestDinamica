<?php

require_once 'controllers/get.controller.php';

$select = $_GET['select'] ?? '*';
$linkTo = $_GET['linkTo'] ?? '*';
$equalTo = $_GET['equalTo'] ?? '*';
$orderBy = $_GET['orderBy'] ?? null;
$orderMode = $_GET['orderMode'] ?? null;
$starAt = $_GET['startAt'] ?? null;
$endAt = $_GET['endAt'] ?? null;
$rel = $_GET['rel'] ?? null;
$type = $_GET['type'] ?? null;
$type_id = $_GET['type_id'] ?? null;
$search = $_GET['search'] ?? null;
$between1 = $_GET['between1'] ?? null;
$between2 = $_GET['between2'] ?? null;
$filterTo = $_GET['filterTo'] ?? null;
$inTo = $_GET['inTo'] ?? null;

$response =  new GetController();

if(isset($_GET['linkTo'] ) && isset( $_GET['equalTo']) && !isset($_GET['rel']) && !isset($_GET['type'])){//Peticiones con filtro
    
    $response->getDataFilter($table,$select,$linkTo,$equalTo,$orderBy,$orderMode,$starAt,$endAt);

   
}//Peticiones sin filtro con relaciones
elseif (isset($_GET['rel']) && isset($_GET['type'])  && isset($_GET['type_id']) && $table=='relations' && !isset($_GET['linkTo'] ) && !isset( $_GET['equalTo']) ) { 
    $response->getDataWithRelations($rel,$type,$type_id,$select,$orderBy,$orderMode,$starAt,$endAt); 


}//Peticiones con filtro con relaciones
elseif (isset($_GET['rel']) && isset($_GET['type'])  && isset($_GET['type_id']) && $table=='relations' && isset($_GET['linkTo'] ) && isset( $_GET['equalTo'])) { 
    $response->getDataWithRelationsFilter($rel,$type,$type_id,$select,$linkTo,$equalTo,$orderBy,$orderMode,$starAt,$endAt); 


}//Peticiones con buscador sin relaciones
elseif(!isset($_GET['rel']) && !isset($_GET['type']) && isset($table) && !isset($_GET['type_id']) && isset($_GET['select']) && isset($_GET['linkTo'] ) && isset( $_GET['search']) ){
   
    $response->getDataSearch($table,$select,$linkTo,$search,$orderBy,$orderMode,$starAt,$endAt);
    
}//Peticiones de busqueda con filtros con relaciones
elseif (isset($_GET['rel']) && isset($_GET['type'])  && isset($_GET['type_id']) && $table=='relations' && isset($_GET['linkTo'] ) && isset( $_GET['search'])) { 
    $response->getDataWithRelationsSearch($rel,$type,$type_id,$select,$linkTo,$search,$orderBy,$orderMode,$starAt,$endAt); 
    

}//Peticiones de busqueda con rangos sin  relaciones
elseif (isset($_GET['between1']) && isset($_GET['between2']) && isset($_GET['linkTo'] ) && isset($_GET['select']) && isset($table) && !isset($_GET['rel']) && !isset($_GET['type'])  && !isset($_GET['type_id'])) { 
    $response->getDataRange($filterTo,$inTo,$linkTo,$table,$between2,$between1,$select,$orderBy,$orderMode,$starAt,$endAt); 
  

}//Peticiones de busqueda con rangos con  relaciones
elseif (isset($_GET['between1']) && isset($_GET['between2']) && isset($_GET['linkTo'] ) && isset($_GET['select']) && $table='relations' && isset($_GET['rel']) && isset($_GET['type'])  && isset($_GET['type_id']) ) { 
    $response->getDataRangeWithRelations($filterTo,$inTo,$linkTo,$rel,$type,$type_id,$between2,$between1,$select,$orderBy,$orderMode,$starAt,$endAt); 
    

}else{   
//peticion sin filtro
$response->getData($table,$select,$orderBy,$orderMode,$starAt,$endAt );  
}