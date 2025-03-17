<?php
    include 'settings.php';
    include 'sql_query.php';


    $method_task = 'tasks.task.list';
    $url_task = $url . $method_task;

    $select_link = $url_task . '?select[]=ID&select[]=PARENT_ID&select[]=TITLE' .
             '&select[]=DESCRIPTION&select[]=STATUS&select[]=GROUP_ID&select[]=STAGE_ID' .
             '&select[]=CREATED_BY&select[]=CREATED_DATE&select[]=RESPONSIBLE_ID' .
             '&select[]=ACCOMPLICES'.
             '&select[]=AUDITORS&select[]=CHANGED_DATE&select[]=DEADLINE&select[]=TAGS&select[]=UF_AUTO_751244333336' .
             '&select[]=UF_AUTO_185560970868&select[]=UF_AUTO_296198624958';


    // function for add tasks to DB
   function addTask($task_array, $add_task){
        $add_task->bind_param('sssssssssssssssssssssss',
                $task_array['id'],
                $task_array['parentId'],
                $task_array['title'],
                $task_array['description'],
                $task_array['status'],
                $task_array['groupId'],
                $task_array['stageId'],
                $task_array['creator']['id'],
                $task_array['creator']['name'],
                $task_array['createdDate'],
                $task_array['RESPONSIBLE_ID'],
                $task_array['RESPONSIBLE_NAME'],
                $task_array['ACCOMPLICES_ID'],
                $task_array['ACCOMPLICES_NAME'],
                $task_array['AUDITORS_ID'],
                $task_array['AUDITORS_NAME'],
                $task_array['changedDate'],
                $task_array['deadline'],
                $task_array['TAGS'],
                $task_array['ufAuto751244333336'],
                $task_array['ufAuto185560970868'],
                $task_array['ufAuto296198624958'],
                $task_array['DATE']
            );

        $add_task->execute();

   }

   print_r('Start download tasks: ' . date('d.m.Y h:i:s', time()));

    // function for get tasks from Bitrix and add to DB
    function getImportTask($select_link, $arrContextOptions, $add_task){
        $url_tasks = file_get_contents($select_link, false, stream_context_create($arrContextOptions));
        $url_tasks = json_decode($url_tasks, TRUE);
        $total = $url_tasks['total'];
    
        if($total > 50){
            $balance = floor($total/ 50);
            $start = $balance;
        }else{
            $start = 0;
        }

        for($i = 0; $i <= $start; $i++){
            $link = $select_link . '&start=' . ($i*50);
            $tasks_json = file_get_contents($link, false, stream_context_create($arrContextOptions));
            $tasks_json = json_decode($tasks_json, TRUE);
            $tasks_json = $tasks_json['result']['tasks'];

            foreach($tasks_json as $task_array){
                    $auditors_id = '';
                    $auditors_name = '';

                    foreach($task_array['auditorsData'] as $auditor){
                        $auditors_id .= $auditor['id'] . ';' ;
                        $auditors_name .= $auditor['name'] . ';' ;
                    }

                    $accomplices_id ='';
                    $accomplices_name ='';
                    foreach($task_array['accomplicesData'] as $accomplice){
                        $accomplices_id .= $accomplice['id'] . ';' ;
                        $accomplices_name .= $accomplice['name'] . ';' ;
                    }

                    $tags = '';
                    foreach($task_array['tags'] as $tag){
                        $tags .= $tag['title'] . ';' ;
                    }

                    $auditors_id = substr($auditors_id, 0, -1); 
                    $auditors_name = substr($auditors_name, 0, -1); 
                    $accomplices_id = substr($accomplices_id, 0, -1); 
                    $accomplices_name = substr($accomplices_name, 0, -1); 
                    $tags = substr($tags, 0, -1);

                    $task_array['RESPONSIBLE_ID'] = $task_array['responsible']['id'];
                    $task_array['RESPONSIBLE_NAME'] = $task_array['responsible']['name'];
                    $task_array['ACCOMPLICES_ID'] = $accomplices_id;
                    $task_array['ACCOMPLICES_NAME'] = $accomplices_name;
                    $task_array['AUDITORS_ID'] = $auditors_id;
                    $task_array['AUDITORS_NAME'] = $auditors_name;
                    $task_array['TAGS'] = $tags;
                    $task_array['DATE'] = date('d.m.Y H:i:s');
                    
                    addTask($task_array, $add_task);

                }
                
        }

    }

    getImportTask($select_link, $arrContextOptions, $add_task);

    print_r("\n");

    print_r('End download tasks: ' . date('d.m.Y h:i:s', time()));


?>