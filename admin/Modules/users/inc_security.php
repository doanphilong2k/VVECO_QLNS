<?php
require_once("../../resource/security/security.php");
$module_id = 3;
$module_name = "Quản lý Tài khoản";
//Check user login...
checkLogged();
//Check access module...
if (checkAccessModule($module_id) != 1) redirect($fs_denypath);
//Declare prameter when insert data
$fs_table = "users";
$id_field = "use_id";
$name_field = "use_name";
$break_page = "{---break---}";

$fs_fieldupload = "use_picture";
$fs_filepath = "../../../data/users/thumb/";
$fs_extension = "gif,jpg,jpe,jpeg,png";
$fs_filesize = 2024;
$imgViewURL = "/data/users/thumb/";
$gender_list = array(
    1 => 'Nam',
    2 => 'Nữ'
);
$userType_list = array(
    1 => 'Giảng Viên/Cán Bộ',
    2 => 'Sinh Viên'
);
$use_type = 2; // Sinh viên
