<?php
    include 'settings.php';
    include 'sql_query.php';


    $method_group = 'sonet_group.get';
    $url_group = $url . $method_group;

    //function for get groups from Bitrix
    // use pagination because we can get only 50 queries at one time
    function getGroups($url_group, $arrContextOptions){
        $url_groups = file_get_contents($url_group, false, stream_context_create($arrContextOptions));
        $url_groups = json_decode($url_groups, TRUE);
        $total = $url_groups['total'];

        if($total > 50){
            $balance = floor($total/ 50);
            $start = $balance;
        }else{
            $start = 0;
        }

        for($i = 0; $i <= $start; $i++){
            $url_groups = file_get_contents(($url_group . '?start=' . ($i*50)), false, stream_context_create($arrContextOptions));
            $url_groups = json_decode($url_groups, TRUE);
            $res[] = $url_groups['result'];
        }

        return($res);
    }

    //function for add groups to DB
    function addGroups($groups_array, $add_group){
        $add_group->bind_param('ssssssssssss',
                    $groups_array['ID'],
                    $groups_array['NAME'],
                    $groups_array['DESCRIPTION'],
                    $groups_array['DATE_CREATE'],
                    $groups_array['DATE_UPDATE'],
                    $groups_array['ACTIVE'],
                    $groups_array['VISIBLE'],
                    $groups_array['OPENED'],
                    $groups_array['CLOSED'],
                    $groups_array['OWNER_ID'],
                    $groups_array['KEYWORDS'],
                    $groups_array['NUMBER_OF_MEMBERS'],
                    );

        $add_group->execute();
    }

    //function for get groups from DB
    function getGroupsDb($id_group){
        $groupd_id_db = [];

        if(empty($id_group)){
            return $groupd_id_db;
        }

        foreach($id_group as $groups_db){
            $groupd_id_db[] = $groups_db;
        }

        return($groupd_id_db);
    }

    //function for update parametrs about groups in DB
    function updateGroups($groups_array, $update_group){
        $update_group->bind_param('ssssssssssss',
                                    $groups_array['NAME'],
                                    $groups_array['DESCRIPTION'],
                                    $groups_array['DATE_CREATE'],
                                    $groups_array['DATE_UPDATE'],
                                    $groups_array['ACTIVE'],
                                    $groups_array['VISIBLE'],
                                    $groups_array['OPENED'],
                                    $groups_array['CLOSED'],
                                    $groups_array['OWNER_ID'],
                                    $groups_array['KEYWORDS'],
                                    $groups_array['NUMBER_OF_MEMBERS'],
                                    $groups_array['ID']);
        $update_group->execute();
    }


    $groups_db = getGroupsDb($id_group);
    $groups_array = getGroups($url_group, $arrContextOptions);
 

    // if DB is empty, need to add all groups
    // else add other groups and update current groups
    if(empty($groups_db)){
        print_r('Groups are empty. Added new groups');

        foreach($groups_array as $group_array){
            foreach($group_array as $group){
                addGroups($group, $add_group);
            }
        }

    }else{
        print_r("Groups aren't empty. Added missing groups and updated all groups");

        for($i = 0; $i <count($groups_db); $i++){
            $id_db[]= $groups_db[$i]['ID'];
        }
        
        foreach($groups_array as $group_array){
            foreach($group_array as $group){
                if(!in_array($group['ID'], $id_db)){
                    addGroups($group, $add_group);
                }
            }
        }
    
        $result = $conn->query('SELECT * FROM projects ORDER BY ID desc');
        $groups_db_new = [];
        while ($row = $result ->fetch_assoc()){
            $groups_db_new[] = $row;
        }

        foreach($groups_array as $group_array){
            foreach($group_array as $group){

                $new_array_group[] = $group;
            }
        }

        for($i = 0; $i < count($new_array_group); $i++){
            if($new_array_group[$i]['NAME'] != $groups_db_new[$i]['NAME'] |
                $new_array_group[$i]['DESCRIPTION'] != $groups_db_new[$i]['DESCRIPTION'] |
                $new_array_group[$i]['CLOSED'] != $groups_db_new[$i]['CLOSED']|
                $new_array_group[$i]['KEYWORDS'] != $groups_db_new[$i]['KEYWORDS']){
        
                updateGroups($new_array_group[$i], $update_group);
            }
        }
    }
    print_r("\n" ."All groups added or updated". " ". date('d.m.Y h:i:s', time()));

?>