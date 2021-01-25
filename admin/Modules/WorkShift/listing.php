<?php
require_once("inc_security.php");
require_once("Excel.php");

$wor_id = getValue("wor_id", "int", "GET", 0);

// Query danh sách Trường
// $list_workshift = new db_query("SELECT * FROM workshift");
// $arrTmp = convert_result_set_2_array($list_workshift->result, "wor_id");
// $arrWorkshift = array(0 => "- Tất cả các Trường -");
// foreach ($arrTmp as $workshift) {
//     $arrWorkshift[$workshift["wor_id"]] = $workshift["wor_name"];
// }

// Query danh sách Khoa theo Trường được chọn
// $sql_faculties = "";
// if ($use_school_id > 0) $sql_faculties .= " AND fac_school_id = " . $use_school_id;
// $list_faculties = new db_query("SELECT * FROM faculties WHERE fac_active = 1" . $sql_faculties);
// $arrTmp = convert_result_set_2_array($list_faculties->result, "fac_id");
// $arrFaculties = array(0 => "- Tất cả các Khoa -");
// foreach ($arrTmp as $faculty) {
//     $arrFaculties[$faculty["fac_id"]] = $faculty["fac_name"];
// }

// Query danh sách Lớp theo Khoa được chọn
// $sql_classes = "";
// if ($use_school_id > 0) $sql_classes .= " AND cls_school_id = " . $use_school_id;
// if ($use_faculty_id > 0) $sql_classes .= " AND cls_faculty_id = " . $use_faculty_id;
// $list_classes = new db_query("SELECT * FROM classes WHERE cls_active = 1" . $sql_classes);
// $arrTmp = convert_result_set_2_array($list_classes->result, "cls_id");
// $arrClasses = array(0 => "- Tất cả các Lớp -");
// foreach ($arrTmp as $class) {
//     $arrClasses[$class["cls_id"]] = $class["cls_name"];
// }

$list                        = new fsDataGird($id_field, $name_field, translate_text("Danh sách Ca làm việc"));
$list->page_size = 50;
/*
1: Ten truong trong bang
2: Tieu de header
3: kieu du lieu
4: co sap xep hay khong, co thi de la 1, khong thi de la 0
5: co tim kiem hay khong, co thi de la 1, khong thi de la 0
*/
//$list->addSearch("Trường","use_school_id","array",$arrSchools, $use_school_id);
//$list->addSearch("Khoa","use_faculty_id","array",$arrFaculties, $use_faculty_id);
//$list->addSearch("Lớp","use_class_id","array",$arrClasses, $use_class_id);
$list->add("wor_id", translate_text("ID"), "text", 0, 1, "");
$list->add($name_field, "Tên ca", "string", 1, 1, "");
$list->add("wor_StartTime", "Thời gian bắt đầu", "text", 0, 1, "");
$list->add("wor_FinishTime", "Thời gian kết thúc", "text", 0, 1, "");
// $list->add("use_active", "Duyệt", "checkbox", 1, 0);
$list->add("", translate_text("Sửa"), "edit");
$list->add("", translate_text("Xóa"), "delete");
$list->ajaxedit($fs_table);


$list->addHTML('
    <div style="padding: 0px 0px 5px 5px">
        <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#form_export"><i class="fa fa-file-excel-o"></i> Xuất Excel Danh sách Ca làm việc</button>
        <button type="button" class="btn btn-xs btn-success" data-toggle="modal" data-target="#form_import"><i class="fa fa-file-excel-o"></i> Nhập Danh sách Ca làm việc từ Excel</button>
        <a class="btn btn-xs btn-link" href="/data/excels/import_users_from_excel_example.xlsx"><i class="fa fa-download" aria-hidden="true"></i> Tải về file Excel mẫu</a>
    </div>
');

$total    = 0;
$db_count = new db_query("SELECT count(*) AS count
                         FROM " . $fs_table . "
                         WHERE 1" . $list->sqlSearch());
if ($row_count = mysqli_fetch_assoc($db_count->result)) {
    $total = $row_count['count'];
}
unset($db_count);
$db_listing = new db_query("SELECT *
                             FROM " . $fs_table . "
                             WHERE 1 " . $list->sqlSearch() . "
                             ORDER BY " . $list->sqlSort() . $id_field . " DESC
                             " . $list->limit($total));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?= $load_header ?>
    <?= $list->headerScript() ?>
</head>

<body topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
    <? /*---------Body------------*/ ?>
    <div id="listing" class="listing">
        <?= $list->showTable($db_listing, $total) ?>
    </div>
    <? /*---------Body------------*/ ?>
</body>

</html>