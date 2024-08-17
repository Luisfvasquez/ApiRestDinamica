<?php

require_once 'conexion.php';
class GetModel
{
    static public function getData($table, $select, $orderBy, $orderMode, $starAt, $endAt)
    { //Peticiones sin filtro

        //Valida si existen las tablas y columnas
        $selectArray = explode(',', $select);


        if (empty(Conexion::getColumnData($table, $selectArray))) {

            return null;
        }

        $instruccion = "SELECT $select FROM $table";

        //Solo se ordenan
        if (isset($orderBy) && isset($orderMode) && $starAt == null && $endAt == null) {
            $instruccion = "SELECT $select FROM $table ORDER BY $orderBy $orderMode";
        }
        //Se ordenan y limitan los datos
        if (isset($orderBy) && isset($orderMode) && $starAt != null && $endAt != null) {
            $instruccion = "SELECT $select FROM $table ORDER BY $orderBy $orderMode LIMIT $starAt,$endAt";
        }
        //Se limitan los datos
        if ($orderBy == null && $orderMode == null && $starAt != null && $endAt != null) {
            $instruccion = "SELECT $select FROM $table LIMIT $starAt,$endAt";
        }

        $stmt = Conexion::conexion()->prepare($instruccion);

        try {
            $stmt->execute();
        } catch (PDOException $Exception) {
            return null;
        }

        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }

    //Peticiones Con filtro
    static public function getDataFilter($table, $select, $linkTo, $equalTo, $orderBy, $orderMode, $starAt, $endAt)
    {
        //Valida si existen las tablas y columnas

        $linkToArray = explode(',', $linkTo);
        $selectArray = explode(',', $select);

        foreach ($linkToArray as $key => $value) {
            array_push($selectArray, $value);
        }

        $selectArray = array_unique($selectArray); //elimina los duplicados

        if (empty(Conexion::getColumnData($table, $selectArray))) {
            return null;
        }

        $equalToArray = explode(',', $equalTo);
        $linkToText = '';

        //Si viene mas de un filtro
        if (count($linkToArray) > 1) {
            foreach ($linkToArray as $key => $value) {
                if ($key > 0) { //Se almacenan las demas sentencias
                    $linkToText  .= "AND " . $value . " = :" . $value . "";
                }
            }
        }
        //Solo filtra
        $instruccion = "SELECT $select FROM $table WHERE $linkToArray[0] = :$linkToArray[0] $linkToText";

        //Solo se ordenan
        if (isset($orderBy) && isset($orderMode) && $starAt == null && $endAt == null) {
            $instruccion = "SELECT $select FROM $table WHERE $linkToArray[0] = :$linkToArray[0] $linkToText ORDER BY $orderBy $orderMode";
        }
        //Se ordenan y limitan los datos
        if (isset($orderBy) && isset($orderMode) && $starAt != null && $endAt != null) {
            $instruccion = "SELECT $select FROM $table WHERE $linkToArray[0] = :$linkToArray[0] $linkToText ORDER BY $orderBy $orderMode LIMIT $starAt,$endAt";
        }
        //Se limitan los datos
        if ($orderBy == null && $orderMode == null && $starAt != null && $endAt != null) {
            $instruccion = "SELECT $select FROM $table WHERE $linkToArray[0] = :$linkToArray[0] $linkToText LIMIT $starAt,$endAt";
        }
        $stmt = Conexion::conexion()->prepare($instruccion);

        foreach ($linkToArray as $key => $value) {
            $stmt->bindParam(":" . $value, $equalToArray[$key]);
        }

        try {
            $stmt->execute();
        } catch (PDOException $Exception) {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }

    //peticiones sin filtro entre tablas relacionadas
    static public function getDataWithRelations($rel, $type, $type_id, $select, $orderBy, $orderMode, $starAt, $endAt)
    { //Peticiones sin filtro
        $relArray = explode(',', $rel);
        $typeArray = explode(',', $type);
        $typeidArray = explode(',', $type_id);
        $innerJoinText = "";




        if (count($relArray) > 1) {
            foreach ($relArray as $key => $value) {
                if (empty(Conexion::getColumnData($value, ['*']))) {
                    return null;
                }
                if ($key > 0) {
                    $innerJoinText  .= "INNER JOIN " . $relArray[$key] . " ON " . $relArray[0] . "." . $typeArray[$key - 1] . " = " . $relArray[$key] . "." . $typeidArray[0] . " ";
                }
            }
            $instruccion = "SELECT $select FROM $relArray[0] $innerJoinText";


            //Solo se ordenan
            if (isset($orderBy) && isset($orderMode) && $starAt == null && $endAt == null) {
                $instruccion = "SELECT $select FROM $relArray[0] $innerJoinText ORDER BY $orderBy $orderMode";
            }
            //Se ordenan y limitan los datos
            if (isset($orderBy) && isset($orderMode) && $starAt != null && $endAt != null) {
                $instruccion = "SELECT $select FROM $relArray[0] $innerJoinText ORDER BY $orderBy $orderMode LIMIT $starAt,$endAt";
            }
            //Se limitan los datos
            if ($orderBy == null && $orderMode == null && $starAt != null && $endAt != null) {
                $instruccion = "SELECT $select FROM $relArray[0] $innerJoinText LIMIT $starAt,$endAt";
            }

            $stmt = Conexion::conexion()->prepare($instruccion);

            try {
                $stmt->execute();
            } catch (PDOException $Exception) {
                return null;
            }

            return $stmt->fetchAll(PDO::FETCH_CLASS);
        } else {
            return null;
        }
    }

    //peticiones con filtro entre tablas relacionadas
    static public function getDataWithRelationsFilter($rel, $type, $type_id, $select, $linkTo, $equalTo, $orderBy, $orderMode, $starAt, $endAt)
    { //Peticiones sin filtro
        //ORGANIZAMOS LOS FILTROS

        $linkToArray = explode(',', $linkTo);
        $equalToArray = explode(',', $equalTo);
        $linkToText = '';



        //Si viene mas de un filtro
        if (count($linkToArray) > 1) {
            foreach ($linkToArray as $key => $value) {

                if ($key > 0) { //Se almacenan las demas sentencias
                    $linkToText  .= "AND " . $value . " = :" . $value . "";
                }
            }
        }


        //ORGANIZAMOS LAS RELACIONES
        $relArray = explode(',', $rel);
        $typeArray = explode(',', $type);

        $typeidArray = explode(',', $type_id);
        $innerJoinText = "";


        if (count($relArray) > 1) {
            foreach ($relArray as $key => $value) {
                if (empty(Conexion::getColumnData($value, ['*']))) {
                    return null;
                }
                if ($key > 0) {
                    $innerJoinText  .= "INNER JOIN " . $relArray[$key] . " ON " . $relArray[0] . "." . $typeArray[$key - 1] . " = " . $relArray[$key] . "." . $typeidArray[0] . " ";
                }
            }
            $instruccion = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkToArray[0] = :$linkToArray[0] $linkToText";



            //Solo se ordenan
            if (isset($orderBy) && isset($orderMode) && $starAt == null && $endAt == null) {
                $instruccion = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkToArray[0] = :$linkToArray[0] $linkToText ORDER BY $orderBy $orderMode";
            }
            //Se ordenan y limitan los datos
            if (isset($orderBy) && isset($orderMode) && $starAt != null && $endAt != null) {
                $instruccion = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkToArray[0] = :$linkToArray[0] $linkToText ORDER BY $orderBy $orderMode LIMIT $starAt,$endAt";
            }
            //Se limitan los datos
            if ($orderBy == null && $orderMode == null && $starAt != null && $endAt != null) {
                $instruccion = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkToArray[0] = :$linkToArray[0] $linkToText LIMIT $starAt,$endAt";
            }

            $stmt = Conexion::conexion()->prepare($instruccion);

            foreach ($linkToArray as $key => $value) {
                $stmt->bindParam(":" . $value, $equalToArray[$key]);
            }

            try {
                $stmt->execute();
            } catch (PDOException $Exception) {
                return null;
            }

            return $stmt->fetchAll(PDO::FETCH_CLASS);
        } else {
            return null;
        }
    }
    //PETICIONES CON BUSCADOR SIN RELACIONES
    static public function getDAtaSearch($table, $select, $linkTo, $search, $orderBy, $orderMode, $starAt, $endAt)
    {
        $linkToArray = explode(',', $linkTo);
        $searchToArray = explode(',', $search);
        $linkToText = '';
        $selectArray = explode(',', $select);

        foreach ($linkToArray as $key => $value) {
            array_push($selectArray, $value);
        }

        $selectArray = array_unique($selectArray);

        if (empty(Conexion::getColumnData($table, $selectArray))) {
            return null;
        }

        //Si viene mas de un filtro
        if (count($linkToArray) > 1) {
            foreach ($linkToArray as $key => $value) {

                if ($key > 0) { //Se almacenan las demas sentencias
                    $linkToText  .= "AND " . $value . " = :" . $value . "";
                }
            }
        }


        $instruccion = "SELECT $select FROM $table WHERE $linkToArray[0] LIKE '%$searchToArray[0]%' $linkToText";

        //Solo se ordenan
        if (isset($orderBy) && isset($orderMode) && $starAt == null && $endAt == null) {
            $instruccion = "SELECT $select FROM $table WHERE $linkToArray[0] LIKE '%$searchToArray[0]%' $linkToText ORDER BY $orderBy $orderMode";
        }
        //Se ordenan y limitan los datos
        if (isset($orderBy) && isset($orderMode) && $starAt != null && $endAt != null) {
            $instruccion = "SELECT $select FROM $table WHERE $linkToArray[0] LIKE '%$searchToArray[0]%' $linkToText ORDER BY $orderBy $orderMode LIMIT $starAt,$endAt";
        }
        //Se limitan los datos
        if ($orderBy == null && $orderMode == null && $starAt != null && $endAt != null) {
            $instruccion = "SELECT $select FROM $table WHERE WHERE $linkToArray[0] LIKE '%$searchToArray[0]%' $linkToText LIMIT $starAt,$endAt";
        }

        $stmt = Conexion::conexion()->prepare($instruccion);
        foreach ($linkToArray as $key => $value) {
            if ($key > 0) {
                $stmt->bindParam(":" . $value, $searchToArray[$key]);
            }
        }

        try {
            $stmt->execute();
        } catch (PDOException $Exception) {
            return null;
        }

        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }
    //Peticiones de busqueda con relaciones
    static public function getDataWithRelationsSearch($rel, $type, $type_id, $select, $linkTo, $search, $orderBy, $orderMode, $starAt, $endAt)
    {
        //ORGANIZAMOS LOS FILTROS      
        $linkToArray = explode(',', $linkTo);
        $searchToArray = explode(',', $search);
        $linkToText = '';

        //Si viene mas de un filtro
        if (count($linkToArray) > 1) {
            foreach ($linkToArray as $key => $value) {

                if ($key > 0) { //Se almacenan las demas sentencias
                    $linkToText  .= "AND " . $value . " = :" . $value . "";
                }
            }
        }


        //ORGANIZAMOS LAS RELACIONES
        $relArray = explode(',', $rel);
        $typeArray = explode(',', $type);

        $typeidArray = explode(',', $type_id);
        $innerJoinText = "";


        if (count($relArray) > 1) {
            foreach ($relArray as $key => $value) {
                if (empty(Conexion::getColumnData($value, ['*']))) {
                    return null;
                }
                if ($key > 0) {
                    $innerJoinText  .= "INNER JOIN " . $relArray[$key] . " ON " . $relArray[0] . "." . $typeArray[$key - 1] . " = " . $relArray[$key] . "." . $typeidArray[0] . " ";
                }
            }
            $instruccion = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkToArray[0] LIKE '%$searchToArray[0]%' $linkToText";



            //Solo se ordenan
            if (isset($orderBy) && isset($orderMode) && $starAt == null && $endAt == null) {
                $instruccion = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkToArray[0] LIKE '%$searchToArray[0]%' $linkToText ORDER BY $orderBy $orderMode";
            }
            //Se ordenan y limitan los datos
            if (isset($orderBy) && isset($orderMode) && $starAt != null && $endAt != null) {
                $instruccion = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkToArray[0] LIKE '%$searchToArray[0]%' $linkToText ORDER BY $orderBy $orderMode LIMIT $starAt,$endAt";
            }
            //Se limitan los datos
            if ($orderBy == null && $orderMode == null && $starAt != null && $endAt != null) {
                $instruccion = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkToArray[0] LIKE '%$searchToArray[0]%' $linkToText LIMIT $starAt,$endAt";
            }

            $stmt = Conexion::conexion()->prepare($instruccion);


            foreach ($linkToArray as $key => $value) {
                if ($key > 0) {
                    $stmt->bindParam(":" . $value, $searchToArray[$key]);
                }
            }

            try {
                $stmt->execute();
            } catch (PDOException $Exception) {
                return null;
            }

            return $stmt->fetchAll(PDO::FETCH_CLASS);
        } else {
            return null;
        }
    }
    //Peticiones de busqueda con rangos sin  relaciones
    static public function getDataRange($filterTo, $inTo, $linkTo, $table, $between2, $between1, $select, $orderBy, $orderMode, $starAt, $endAt)
    {
        $linkToArray = explode(',', $linkTo);
        $selectArray = explode(',', $select);
        if ($filterTo != null) {

            $filterTotArray = explode(',', $filterTo);
        } else {
            $filterTotArray = array();
        }
        foreach ($linkToArray as $key => $value) {
            array_push($selectArray, $value);
        }
        foreach ($filterTotArray as $key => $value) {
            array_push($selectArray, $value);
        }

        $selectArray = array_unique($selectArray);

        if (empty(Conexion::conexion($table, $selectArray))) {
            return null;
        }

        $filter = '';
        if ($linkTo != null && $inTo != null) {
            $filter = "AND " . $filterTo . " IN (" . $inTo . ")";
        }

        $instruccion = "SELECT $select FROM $table WHERE $linkTo  BETWEEN '$between1' AND '$between2' $filter";


        //Solo se ordenan
        if (isset($orderBy) && isset($orderMode) && $starAt == null && $endAt == null) {
            $instruccion = "SELECT $select FROM $table WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter ORDER BY $orderBy $orderMode";
        }
        //Se ordenan y limitan los datos
        if (isset($orderBy) && isset($orderMode) && $starAt != null && $endAt != null) {
            $instruccion = "SELECT $select FROM $table WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter ORDER BY $orderBy $orderMode LIMIT $starAt,$endAt";
        }
        //Se limitan los datos
        if ($orderBy == null && $orderMode == null && $starAt != null && $endAt != null) {
            $instruccion = "SELECT $select FROM $table WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter LIMIT $starAt,$endAt";
        }

        $stmt = Conexion::conexion()->prepare($instruccion);
        try {
            $stmt->execute();
        } catch (PDOException $Exception) {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }
    //Peticiones de busqueda con rangos con relaciones
    static public function getDataRangeWithRelations($filterTo, $inTo, $linkTo, $rel, $type, $type_id, $between2, $between1, $select, $orderBy, $orderMode, $starAt, $endAt)
    {

        $filter = '';
        if ($linkTo != null && $inTo != null) {
            $filter = "AND " . $filterTo . " IN (" . $inTo . ")";
        }
        $relArray = explode(',', $rel);
        $typeArray = explode(',', $type);

        $typeidArray = explode(',', $type_id);
        $innerJoinText = "";


        if (count($relArray) > 1) {
            foreach ($relArray as $key => $value) {
                if (empty(Conexion::getColumnData($value, ['*']))) {
                    return null;
                }
                if ($key > 0) {
                    $innerJoinText  .= "INNER JOIN " . $relArray[$key] . " ON " . $relArray[0] . "." . $typeArray[$key - 1] . " = " . $relArray[$key] . "." . $typeidArray[0] . " ";
                }
            }
        }


        $instruccion = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkTo  BETWEEN '$between1' AND '$between2' $filter";


        //Solo se ordenan
        if (isset($orderBy) && isset($orderMode) && $starAt == null && $endAt == null) {
            $instruccion = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter ORDER BY $orderBy $orderMode";
        }
        //Se ordenan y limitan los datos
        if (isset($orderBy) && isset($orderMode) && $starAt != null && $endAt != null) {
            $instruccion = "SELECT $select FROM $relArray[0]$innerJoinText WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter ORDER BY $orderBy $orderMode LIMIT $starAt,$endAt";
        }
        //Se limitan los datos
        if ($orderBy == null && $orderMode == null && $starAt != null && $endAt != null) {
            $instruccion = "SELECT $select FROM $relArray[0] $innerJoinText WHERE $linkTo BETWEEN '$between1' AND '$between2' $filter LIMIT $starAt,$endAt";
        }

        $stmt = Conexion::conexion()->prepare($instruccion);
        try {
            $stmt->execute();
        } catch (PDOException $Exception) {
            return null;
        }

        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }
}
