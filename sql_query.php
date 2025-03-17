<?php
    // SQL query for get all information about projects
    $group_db = $conn->query('SELECT SQL_NO_CACHE * FROM projects WHERE NAME NOT REGEXP "ТЕСТ|Тест|тест|test|Test|TEST" ORDER BY ID desc');
    $id_group = $conn->query('SELECT SQL_NO_CACHE * FROM projects ORDER BY ID desc');


    // SQL query for add projects
    $add_group = $conn->prepare('INSERT INTO projects (ID, NAME, DESCRIPTION,
                            DATE_CREATE, DATE_UPDATE, ACTIVE, VISIBLE, OPENED, 
                            CLOSED, OWNER_ID, KEYWORDS, NUMBER_OF_MEMBERS)
                            VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

    // SQL query for update projects
    $update_group = $conn->prepare('UPDATE projects SET NAME = ?, DESCRIPTION = ?,
                            DATE_CREATE = ?, DATE_UPDATE = ?, ACTIVE = ?, VISIBLE = ?, OPENED = ?, 
                            CLOSED = ?, OWNER_ID = ?, KEYWORDS = ?, NUMBER_OF_MEMBERS = ? WHERE ID = ?');

    // SQL query for get stages of projects
    $stages_db = $conn->query('SELECT * FROM stagescache');
    
    // SQL query for choose stage of projects
    $choose_stages_db = $conn->prepare('SELECT * FROM stagescache WHERE ID = ? AND GROUP_ID = ?');

    // SQL query for add stages of projects
    $add_stage = $conn->prepare('INSERT INTO stagescache (ID, GROUP_ID, TITLE)
                            VALUES(?, ?, ?)');

    // SQL query for update stages of projects
    $update_stage = $conn->prepare('UPDATE stagescache SET TITLE = ? WHERE ID = ?');

    // SQL query for add tasks of projects
    $add_task = $conn->prepare('INSERT INTO tasks (ID, PARENT_ID, TITLE, DESCRIPTION,
                                STATUS, GROUP_ID, STAGE_ID, CREATOR_ID, CREATED_NAME,
                                CREATED_DATE, RESPONSIBLE_ID, RESPONSIBLE_NAME, 
                                ACCOMPLICES_ID, ACCOMPLICES_NAME, AUDITORS_ID, 
                                AUDITORS_NAME, CHANGED_DATE, DEADLINE, TAGS, 
                                UF_AUTO_751244333336, UF_AUTO_185560970868, UF_AUTO_296198624958, 
                                DATE)  
                            VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    
    // SQL query for add users of web-site
    $add_user = $conn->prepare('INSERT INTO users (ID, NAME, SECOND_NAME,
                                LAST_NAME, EMAIL, UF_DEPARTMENT, WORK_POSITION)
                                VALUES(?, ?, ?, ?, ?, ?, ?)');

    // SQL query for get users from DB
    $id_user = $conn->query('SELECT SQL_NO_CACHE * FROM users ORDER BY ID asc');
    
    // SQL query for update infromation of users
    $update_user = $conn->prepare('UPDATE users SET NAME = ?, SECOND_NAME = ?,
                                    LAST_NAME = ?, EMAIL = ?, UF_DEPARTMENT = ?, WORK_POSITION = ? WHERE ID = ?');
?>