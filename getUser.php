<?php
    include 'settings.php';
    include 'sql_query.php';

    $method_user = 'user.get';
    $url_users = $url . $method_user;


    function getUser($url_users, $arrContextOptions){
        $url_user = file_get_contents($url_users, false, stream_context_create($arrContextOptions));
        $url_user = json_decode($url_user, TRUE);
        $total = $url_user['total'];

        if($total > 50){
            $balance = floor($total/ 50);
            $start = $balance;
        }else{
            $start = 0;
        }

        for($i = 0; $i <= $start; $i++){
            $link_user = $url_users . '?start=' . ($i*50);
            $array_users = file_get_contents($link_user, false, stream_context_create($arrContextOptions));
            $array_users = json_decode($array_users, TRUE);
            $res[] = $array_users['result'];
        }

        return($res);
    }


    function addUser($users_array, $add_user){
        $add_user->bind_param('sssssss',
                    $users_array['ID'],
                    $users_array['NAME'],
                    $users_array['SECOND_NAME'],
                    $users_array['LAST_NAME'],
                    $users_array['EMAIL'],
                    $users_array['UF_DEPARTMENT'],
                    $users_array['WORK_POSITION'],
                    );

        $add_user->execute();
    }


    //function for get users from DB
    function getUsersDb($id_user){
        $user_id_db = [];

        if(empty($id_user)){
            return $user_id_db;
        }

        foreach($id_user as $users_db){
            $user_id_db[] = $users_db;
        }

        return($user_id_db);
    }


    function updateUsers($users_array, $update_user){
        $update_user->bind_param('sssssss',
                        $users_array['ID'],
                        $users_array['NAME'],
                        $users_array['SECOND_NAME'],
                        $users_array['LAST_NAME'],
                        $users_array['EMAIL'],
                        $users_array['UF_DEPARTMENT'],
                        $users_array['WORK_POSITION'],
                    );
        $update_user->execute();
    }

    $users_db = getUsersDb($id_user);
    $users_array = getUser($url_users, $arrContextOptions);
    print_r($users_array);

    if(empty($users_db)){
        print_r('Users are empty. Added new users');

        foreach($users_array as $user_array){
            foreach($user_array as $user){
                $depart = '';
                foreach($user['UF_DEPARTMENT'] as $departament){
                    $depart .= $departament . ';';
                }
                $depart = substr($depart, 0, -1);
                $user['UF_DEPARTMENT'] = $depart;
                addUser($user, $add_user);
            }
        }
    }
    // else{
        print_r("Groups aren't empty. Added missing groups and updated all groups");

        for($i = 0; $i <count($users_db); $i++){
            $id_person[]= $users_db[$i]['ID'];
        }

        foreach($users_array as $user_array){
            foreach($user_array as $user){
                if(!in_array($user['ID'], $id_person)){
                    $depart = '';
                    foreach($user['UF_DEPARTMENT'] as $departament){
                        $depart .= $departament . ';';
                    }
                    $depart = substr($depart, 0, -1);
                    $user['UF_DEPARTMENT'] = $depart;
                    addUser($user, $add_user);
                }
            }
        }     
        
    
        // $result = $conn->query('SELECT * FROM projects ORDER BY ID desc');
        // $groups_db_new = [];
        // while ($row = $result ->fetch_assoc()){
        //     $groups_db_new[] = $row;
        // }

        // for($i = 0; $i < count($groups_array['0']); $i++){
        //         if($groups_array['0'][$i]['NAME'] != $groups_db_new[$i]['NAME'] |
        //             $groups_array['0'][$i]['DESCRIPTION'] != $groups_db_new[$i]['DESCRIPTION'] |
        //             $groups_array['0'][$i]['CLOSED'] != $groups_db_new[$i]['CLOSED']|
        //             $groups_array['0'][$i]['KEYWORDS'] != $groups_db_new[$i]['KEYWORDS']){
                    
        //                 updateGroups($groups_array['0'][$i], $update_group);
        //         }
        // }
    }

?>