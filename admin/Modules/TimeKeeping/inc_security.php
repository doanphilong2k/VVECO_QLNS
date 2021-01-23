<?php
    
    require_once("../../resource/security/security.php");
    $module_id = 12;
    //Check user login...
    checkLogged();
    //Check access module...
    if (checkAccessModule($module_id) != 1) {redirect($fs_denypath);}
    
    $fs_table = "member_checkin";
    $id_field = "id";
    $name_field = "checkin_time";
    
    $list_memCheckin = new db_query("SELECT * FROM member_checkin");
?>