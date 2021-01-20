<?php
include("inc_security.php");
checkAddEdit("add");

//Khai báo biến khi thêm mới
$after_save_data = getValue("after_save_data", "str", "POST", "listing.php");
$add = "add.php";
$listing = "listing.php";
$fs_title = "Thêm mới Sinh Viên";
$fs_action = getURL();
$fs_redirect = base64_decode(getValue("url", "str", "GET", base64_encode($after_save_data)));
$fs_errorMsg = "";

$use_active = 0;
$use_gender = 1;
$use_birthdays = date('d-m-Y');
$use_created_time = time();
$use_updated_time = time();

$school_id = getValue("school_id", "int", "POST", 0);
$faculty_id = getValue("faculty_id", "int", "POST", 0);
$class_id = getValue("class_id", "int", "POST", 0);
$use_name = getValue("use_name", "str", "POST", "", 3);
$use_code = getValue("use_code", "str", "POST", "", 3);
$use_code_md5 = md5($use_code);
$use_idnumber = getValue("use_idnumber", "str", "POST", "", 3);
$use_idnumber_md5 = md5($use_idnumber);
$use_active = getValue("use_active", "int", "POST", $use_active);
$use_gender = getValue("use_gender", "int", "POST", $use_gender);
$use_birthdays = getValue("use_birthdays", "str", "POST", $use_birthdays);
$use_birthdays = strtotime(str_replace('/', '-', $use_birthdays));
if (empty($use_birthdays)) {
    $use_birthdays = time();
}

//Call Class generate_form();
$myform = new generate_form();
$myform->add("use_school_id", "school_id", 1, 1, 0, 1, "Bạn chưa chọn Trường.", 0, "");
$myform->add("use_faculty_id", "faculty_id", 1, 1, 0, 1, "Bạn chưa chọn Khoa.", 0, "");
$myform->add("use_class_id", "class_id", 1, 1, 0, 1, "Bạn chưa chọn Lớp.", 0, "");
$myform->add("use_name", "use_name", 0, 1, "", 1, translate("Họ và Tên không được để trống."), 0, "");
$myform->add("use_birthdays", "use_birthdays", 0, 1, "", 1, translate("Bạn chưa nhập Ngày sinh"));
$myform->add("use_gender", "use_gender", 1, 1, "");

$use_salt = md5(rand(100000, 999999));
$use_password = md5('123456' . $use_salt);
$myform->add("use_password", "use_password", 0, 1, '', 0, "", 0, "");
$myform->add("use_salt", "use_salt", 0, 1, '', 0, "", 0, "");
$myform->add("use_code", "use_code", 0, 1, "", 1, translate("Mã Sinh Viên không được để trống"), 0, "0");
$myform->add("use_code_md5", "use_code_md5", 0, 1, "", 0, translate("Mã Sinh Viên không được để trống"), 1, "Mã Sinh Viên đã tồn tại trong hệ thống.");
$myform->add("use_idnumber", "use_idnumber", 0, 1, "", 1, translate("Số CMND/Hộ chiếu không được để trống."), 0, "0");
$myform->add("use_idnumber_md5", "use_idnumber_md5", 0, 1, "", 0, translate("Số CMND/Hộ chiếu không được để trống."), 1, "Số CMND/Hộ chiếu đã tồn tại trong hệ thống.");
$myform->add("use_type", "use_type", 1, 1, 1, 0, "", 0, "");
$myform->add("use_active", "use_active", 1, 1, 1, 0, "", 0, "");
$myform->add("use_created_time", "use_created_time", 1, 1, 1, 0, "", 0, "");
$myform->add("use_updated_time", "use_updated_time", 1, 1, 1, 0, "", 0, "");
$myform->add("admin_id", "admin_id", 1, 1, 1, 0, "", 0, "");
$myform->addTable($fs_table);

$action = getValue("action", "str", "POST", "");

//Check $action for insert new data
if ($action == "execute") {
    //Check form data
    $fs_errorMsg .= $myform->checkdata();

    if ($fs_errorMsg == "") {
        //Insert to database
        $myform->removeHTML(1);
        $db_insert = new db_execute($myform->generate_insert_SQL());
        unset($db_insert);

        //Redirect after insert complate
        redirect($fs_redirect);
    }
    //End if($fs_errorMsg == "")
}//End if($action == "insert")

// Query danh sách Trường
$list_schools = new db_query("SELECT * FROM schools WHERE sch_active = 1");

// Query danh sách Khoa theo Trường được chọn
$list_faculties = new db_query("SELECT * FROM faculties WHERE fac_active = 1 AND fac_school_id=" . $school_id);

// Query danh sách Lớp theo Khoa được chọn
$list_classes = new db_query("SELECT * FROM classes WHERE cls_active = 1 AND cls_faculty_id=" . $faculty_id);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?= $load_header ?>
    <?php
    //add form for javacheck
    $myform->addFormname("add");
    $myform->checkjavascript();
    //chuyển các trường thành biến để lấy giá trị thay cho dùng kiểu getValue
    $myform->evaluate();
    $fs_errorMsg .= $myform->strErrorField;

    ?>
</head>

<body topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
<? /*------------------------------------------------------------------------------------------------*/ ?>
<?= template_top($fs_title) ?>
<? /*------------------------------------------------------------------------------------------------*/ ?>
<p align="center" style="padding-left:10px;">
    <?php
    $form = new form();
    $form->create_form("add", $fs_action, "post", "multipart/form-data", 'onsubmit="validateForm(); return false;"');
    $form->create_table();
    ?>
    <?= $form->text_note('Những ô có dấu sao (<font class="form_asterisk">*</font>) là bắt buộc phải nhập.') ?>
    <?= $form->errorMsg($fs_errorMsg) ?>
    <?= $form->select_db("Chọn Trường", "school_id", "school_id", $list_schools, "sch_id", "sch_name", $school_id, "Chọn Trường", 1, "250", 1, 0, 'onchange="loadFaculties();"', "") ?>
    <tr>
        <td class="form_name"><font class="form_asterisk">* </font>Chọn Khoa :</td>
        <td class="form_text" id="listFaculties">
            <select class="form-control" title="Chọn Khoa" id="faculty_id" name="faculty_id" style="width:250px" size="1" onchange="loadClasses();">
                <option value="">- Chọn Khoa -</option>
                <?
                $arrFaculties = convert_result_set_2_array($list_faculties->result, "fac_id");
                foreach($arrFaculties as $key => $value){
                    $selected = ($value["fac_id"] == $faculty_id ? " selected" : "");
                    ?>
                    <option value="<?=$value["fac_id"]?>"<?=$selected?>><?=$value["fac_name"]?></option>
                <?
                }
                ?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="form_name"><font class="form_asterisk">* </font>Chọn Lớp :</td>
        <td class="form_text" id="listClasses">
            <select class="form-control" title="Chọn Lớp" id="class_id" name="class_id" style="width:250px" size="1">
                <option value="">- Chọn Lớp -</option>
                <?
                $arrClasses = convert_result_set_2_array($list_classes->result, "cls_id");
                foreach($arrClasses as $key => $value){
                    $selected = ($value["cls_id"] == $class_id ? " selected" : "");
                    ?>
                    <option value="<?=$value["cls_id"]?>"<?=$selected?>><?=$value["cls_name"]?></option>
                <?
                }
                ?>
            </select>
        </td>
    </tr>
    <?= $form->text("Họ và Tên", "use_name", "use_name", $use_name, "Họ và Tên", 1, 250, "", 255, "", "", "") ?>
    <?= $form->text("Mã Sinh Viên", "use_code", "use_code", $use_code, "Mã Sinh Viên", 1, 250, "", 50, "", "", "") ?>
    <?= $form->text("Số CMND/Hộ chiếu", "use_idnumber", "use_idnumber", $use_idnumber, "Số CMND/Hộ chiếu", 1, 250, "", 50, "", "", "") ?>
    <?= $form->radio("Giới tính", "nam" . $form->ec . "nu", "use_gender", "1" . $form->ec . "2", 1, "Nam" . $form->ec . "Nữ", 0, $form->ec, ""); ?>
    <tr>
        <td class="form_name">Ngày sinh :</td>
        <td class="form_text">
            <input type="text" class="form-control date" name="use_birthdays" id="use_birthdays"
                   onkeypress="displayDatePicker('use_birthdays', this);"
                   onclick="displayDatePicker('use_birthdays', this);"
                   onfocus="if(this.value=='Enter Ngày tạo') this.value=''"
                   onblur="if(this.value=='') this.value='Enter Ngày tạo'"
                   value="<?= date('d/m/Y', $use_birthdays) ?>">
        </td>
    </tr>

    <?= $form->checkbox("Kích hoạt", "use_active", "use_active", 1, $use_active, "Kích hoạt", 0, "", "") ?>
    <?= $form->radio("Sau khi lưu dữ liệu", "add_new" . $form->ec . "return_listing", "after_save_data", $add . $form->ec . $listing, $after_save_data, "Thêm mới" . $form->ec . "Quay về danh sách", 0, $form->ec, ""); ?>
    <?= $form->button("submit" . $form->ec . "reset", "submit" . $form->ec . "reset", "submit" . $form->ec . "reset", "Cập nhật" . $form->ec . "Làm lại", "Cập nhật" . $form->ec . "Làm lại", '' . $form->ec . '', ""); ?>
    <?= $form->hidden("action", "action", "execute", ""); ?>

    <?php
    $form->close_table();
    $form->close_form();
    unset($form);
    ?>
    <script type="text/javascript">
        /**
         * ajax load danh sách Khoa
         */
        function loadFaculties(){
            var schoolID = $("#school_id").val();
            $( "#listFaculties" ).html("<img src='/images/loading_process.gif' height='34px' />");

            setTimeout(function(){
                $( "#listFaculties" ).load("/ajax/load_faculties.php?schoolID=" + schoolID);
            }, 500);


        }

        /**
         * ajax load danh sách Lớp
         */
        function loadClasses(){
            var facultyID = $("#faculty_id").val();
            $( "#listClasses" ).html("<img src='/images/loading_process.gif' height='34px' />");

            setTimeout(function(){
                $( "#listClasses" ).load("/ajax/load_classes.php?facultyID=" + facultyID);
            }, 500);


        }
    </script>
</p>
<? /*------------------------------------------------------------------------------------------------*/ ?>
<?= template_bottom() ?>
<? /*------------------------------------------------------------------------------------------------*/ ?>
</body>
</html>
