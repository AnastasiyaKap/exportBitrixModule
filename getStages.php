<?php
    include 'settings.php';
    include 'sql_query.php';
   
    $method_stage = 'task.stages.get?entityId=';
    $url_stages = $url . $method_stage;

    // function for get groups from DB
    function getGroupsDb($group_db){
        foreach($group_db as $groups_db){
            $groupd_id_db[] = $groups_db;
        }
        return($groupd_id_db);
    }

    // function for get stages from Bitrix
    function getStages($group_db, $url_stages, $arrContextOptions){
        $stages_array = [];
        for($i = 0; $i < count($group_db); $i++){
            $url_i = $url_stages . $group_db[$i];
            $stages = file_get_contents($url_i, false, stream_context_create($arrContextOptions));
            $stages = json_decode($stages, TRUE);
            $stages = $stages['result'];

            foreach($stages as $stage){
                $stages_array[] = [
                  'ID' => $stage['ID'],
                  'GROUP_ID' => $group_db[$i],
                  'TITLE'=> $stage['TITLE']
                ];
            }
        }
        return($stages_array);
    }

    // function for add stages in DB
    function addStages($array_stage, $add_stage){
            $add_stage->bind_param('sss', $array_stage['ID'], $array_stage['GROUP_ID'], $array_stage['TITLE']);
            $add_stage->execute();
    }

    // function for get stages from DB
    function getStageDb($stages_db){
        $all_stages_db = [];
        if(empty($stages_db)){
            return $all_stages_db;
        }
        foreach($stages_db as $stage){
            $all_stages_db[] = $stage;
        }
        return($all_stages_db);
    }
    
    // function for update stages in DB
    function updateStages($array_stage, $update_stage){
        $update_stage->bind_param('ss', $array_stage['TITLE'], $array_stage['ID']);
        $update_stage->execute();
    }


    $groups_db = getGroupsDb($group_db);
    for($i = 0; $i <count($groups_db); $i++){
        $id_db[]= $groups_db[$i]['ID'];
    }

    $stages_array = getStages($id_db, $url_stages, $arrContextOptions);
    $all_stages_db = getStageDb($stages_db);

    for($i = 0; $i < count($stages_array); $i++){
        $choose_stages_db->bind_param('ss', $stages_array[$i]['ID'], $stages_array[$i]['GROUP_ID']);
        $choose_stages_db->execute();        
        $result = $choose_stages_db->get_result()->fetch_all(MYSQLI_ASSOC);
        if(empty($result)){
            print_r('Stages are empty. Added new stages or missing stages');
            addStages($stages_array[$i],  $add_stage);
        }else{
            print_r('Stages are not empty. Updated stages');
            updateStages($stages_array[$i], $update_stage);
        }
    }
    print_r("\n" ."All stages added or updated". " ". date('d.m.Y h:i:s', time()));

?>