<?php
    include 'settings.php';
    include 'sql_query.php';

    $method_department = 'department.get?sort=ID&order=ASC';
    $url_dprt = $url . $method_department;

    //function for get departament from Bitrix
    function getDepartament($url_dprt, $arrContextOptions){
        $url_dprts = file_get_contents($url_dprt, false, stream_context_create($arrContextOptions));
        $url_dprts = json_decode($url_dprts, TRUE);
        $total = $url_dprts['total'];

        if($total > 50){
            $balance = floor($total/ 50);
            $start = $balance;
        }else{
            $start = 0;
        }

        for($i = 0; $i <= $start; $i++){
            $url_departaments = file_get_contents(($url_dprt . '&start=' . ($i*50)), false, stream_context_create($arrContextOptions));
            $url_departaments = json_decode($url_departaments, TRUE);
            $res[] = $url_departaments['result'];
        }

        foreach($res as $items){
            foreach($items as $item){
                $result[] = $item;
            }
        }

        return($result);
    }

    //function for add departament to DB
    function addDepartament($dprt_array, $add_dprt){
        $add_dprt->bind_param('sss',
                    $dprt_array['ID'],
                    $dprt_array['NAME'],
                    $dprt_array['PARENT'],
                    );

        $add_dprt->execute();
    }

    //function for get departament from DB
    function getDepartamentDb($id_dprt){
        $dprt_id_db = [];

        if(empty($id_dprt)){
            return $dprt_id_db;
        }

        foreach($id_dprt as $dprts_db){
            $dprt_id_db[] = $dprts_db;
        }

        return($dprt_id_db);
    }

    //function for update departament in DB
    function updateDepartament($dprt_array, $update_dprt){
        $update_dprt->bind_param('sss',
                                    $dprt_array['NAME'],
                                    $dprt_array['PARENT'],
                                    $dprt_array['ID']);
        $update_dprt->execute();
    }


    $dprt_db = getDepartamentDb($id_dprt);
    $dprts_array = getDepartament($url_dprt, $arrContextOptions);

    if(empty($dprt_db)){
        print_r('Departaments are empty. Added new departaments');

        foreach($dprts_array as $dprt_array){
            addDepartament($dprt_array, $add_dprt);
        }

    }else{
        print_r("Departaments aren't empty. Added missing departaments and updated all departaments");

        for($i = 0; $i <count($dprt_db); $i++){
            $id_db[]= $dprt_db[$i]['ID'];
        }
        
        foreach($dprts_array as $dprt_array){
            if(!in_array($dprt_array['ID'], $id_db)){
                addDepartament($dprt_array, $add_group);
            }
        }

    
        $result = $conn->query('SELECT * FROM department');
        $dprtmnts_db_new = [];
        while ($row = $result ->fetch_assoc()){
            $dprtmnts_db_new[] = $row;
        }

        foreach($dprts_array as $dprt_array){
            $new_array_dprts[] = $dprt_array;
        }
        
     
        for($i = 0; $i < count($new_array_dprts); $i++){
            if($new_array_dprts[$i]['NAME'] != $dprtmnts_db_new[$i]['NAME'] |
                $new_array_dprts[$i]['PARENT'] != $dprtmnts_db_new[$i]['PARENT']){
                    updateGroups($new_array_dprts[$i], $update_dprt);
            }
        }

    }

?>