<?php
require_once("../../resource/security/security.php");

//Check user login...
checkLogged();
//Check access module...
if (checkAccessModule($module_id) != 1) redirect($fs_denypath);
//Declare prameter when insert data
$fs_table = "workshift";
$id_field = "wor_idShift";
$name_field = "wor_Name";
?>