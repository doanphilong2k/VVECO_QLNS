<?php
    echo"Hello";
    require_once("inc_security.php");
    $db_listing = new db_query("SELECT * FROM member_checkin WHERE checkin_time = '2020/07/06'");
    $list    = new fsDataGird($id_field, $name_field, translate_text("Danh sách các Khoa"));
    if(mysqli_num_rows($db_listing->result) < 0){
        echo"Khong co du lieu!";
    }

    while ($listing = mysqli_fetch_assoc($db_listing->result)) {
        $str_category_name	= "";
        $str_category_name	.= $listing["checkin_time"];
        $fac_school_id[$listing['id']]	= $str_category_name;
    }
    unset($db_listing);
    
    $list->add("id", translate_text("ID"), "array", 0, 1);
    $list->add($name_field, translate_text("Khoa"), "string", 0, 1);
    $list->add("fac_active", translate_text("Kích hoạt"), "checkbox", 1, 0);
    $list->add("fac_create_time", "Ngày tạo", "date", 1, 0);
    $list->add("", translate_text("Sửa"), "edit");
    $list->add("", translate_text("Xóa"), "delete");
    
    $list->ajaxedit($fs_table);
    
    $db_count = new db_query("SELECT count(*) AS count
                                            FROM " . $fs_table . "
                                            WHERE 1 " . $list->sqlSearch());
    $total    = 0;
    if ($row = mysqli_fetch_assoc($db_count->result)) {
        $total = intval($row['count']);
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
?>