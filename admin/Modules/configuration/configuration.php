<?
	include("inc_security.php");

	//Khai báo biến khi thêm mới
	$fs_title				= "Cấu hình Website";
	$fs_action				= getURL();
	$fs_redirect			= getURL();
	$fs_errorMsg			= "";

	//Get data edit
	$record_id				= $lang_id;
	$db_edit					= new db_query("SELECT * FROM " . $fs_table . " WHERE " . $id_field . " = " . $record_id);
	if(mysqli_num_rows($db_edit->result) == 0){
		//Redirect if can not find data
		redirect($fs_error);
	}
	$edit						= mysqli_fetch_assoc($db_edit->result);
	unset($db_edit);
	$con_site_title = getValue("con_site_title", "str", "POST", $edit["con_site_title"]);

	$myform = new generate_form();
	$myform->add("con_admin_email", "con_admin_email", 0, 0, $edit["con_admin_email"], 0, "", 0, "");
	$myform->add("con_site_title", "con_site_title", 0, 1, " ", 1, "Bạn chưa nhập tiêu đề cho website", 0, "");
	$myform->add("con_meta_keywords", "con_meta_keywords", 0, 0, $edit["con_meta_keywords"], 0, "", 0, "");
	$myform->add("con_meta_description", "con_meta_description", 0, 0, $edit["con_meta_description"], 0, "", 0, "");
	$myform->add("con_link_fb", "con_link_fb", 0, 0, $edit["con_link_fb"], 0, "", 0, "");
	$myform->add("con_link_twiter", "con_link_twiter", 0, 0, $edit["con_link_twiter"], 0, "", 0, "");
	$myform->add("con_link_insta", "con_link_insta", 0, 0, $edit["con_link_insta"], 0, "", 0, "");
	$myform->add("con_page_fb", "con_page_fb", 0, 0, $edit["con_page_fb"], 0, "", 0, "");
	$myform->add("con_hotline", "con_hotline", 0, 0, $edit["con_hotline"], 0, "", 0, "");
	$myform->add("con_address", "con_address", 0, 0, $edit["con_address"], 0, "", 0, "");
   	$myform->add("con_background_color", "con_background_color", 0, 0, $edit["con_background_color"], 0, "", 0, "");
   	$myform->add("con_info_payment", "con_info_payment", 0, 0, $edit["con_info_payment"], 0, "", 0, "");
   	$myform->add("con_fee_transport", "con_fee_transport", 0, 0, $edit["con_fee_transport"], 0, "", 0, "");
   	$myform->add("con_contact_sale", "con_contact_sale", 0, 0, $edit["con_contact_sale"], 0, "", 0, "");
   	$myform->add("con_info_company", "con_info_company", 0, 0, $edit["con_info_company"], 0, "", 0, "");
   	$myform->add("con_buy_shop", "con_buy_shop", 0, 0, $edit["con_buy_shop"], 0, "", 0, "");
   	$myform->add("con_map", "con_map", 0, 0, $edit["con_map"], 0, "", 0, "");
   	$myform->add("con_footer", "con_footer", 0, 0, $edit["con_footer"], 0, "", 0, "");
   	$myform->add("con_content_ship", "con_content_ship", 0, 0, $edit["con_content_ship"], 0, "", 0, "");
	//Add table insert data (add sau khi add het các trường để check lỗi)
	$myform->addTable($fs_table);
	$action					= getValue("action", "str", "POST", "");
	//Check $action for insert new data
	if($action == "execute"){
		//Check form data
		$fs_errorMsg .= $myform->checkdata();

      //Get $filename and upload
   	$filename	= "";
   	if($fs_errorMsg == ""){
   		$upload_image 			= new upload_image();
			$upload_image->upload($fs_fieldupload, $fs_filepath, $fs_extension, $fs_filesize);
   		$filename		= $upload_image->file_name;
   		$fs_errorMsg  .= $upload_image->warning_error;
   	}

      if($filename != "") {
			$$fs_fieldupload = $filename;
			$myform->add($fs_fieldupload, $fs_fieldupload, 0, 1, "", 0, "", 0, "");
		}//End if($filename != "")

		//Get $filename and upload bg detail
   	$filename2	= "";
   	if($fs_errorMsg == ""){
   		$upload_image2 			= new upload_image();
			$upload_image2->upload($fs_fieldupload2, $fs_filepath, $fs_extension, $fs_filesize);
   		$filename2		= $upload_image2->file_name;
   		$fs_errorMsg  .= $upload_image2->warning_error;
   	}

      if($filename2 != "") {
			$$fs_fieldupload2 = $filename2;
			$myform->add($fs_fieldupload2, $fs_fieldupload2, 0, 1, "", 0, "", 0, "");
		}//End if($filename2 != "")
		$filename3 = "";
    if ($fs_errorMsg == "") {
        $upload_image3 = new upload_image();
        $upload_image3->upload($fs_fieldupload3, $fs_filepath, $fs_extension, $fs_filesize);
        $filename3 = $upload_image3->file_name;
        $fs_errorMsg .= $upload_image3->warning_error;
    }

    if ($filename3 != "") {
        $$fs_fieldupload3 = $filename3;
        $myform->add($fs_fieldupload3, $fs_fieldupload3, 0, 1, "", 0, "", 0, "");
    }//End if($filename2 != "")

    $filename4 = "";
    if ($fs_errorMsg == "") {
        $upload_image4 = new upload_image();
        $upload_image4->upload($fs_fieldupload4, $fs_filepath, $fs_extension, $fs_filesize);
        $filename4 = $upload_image4->file_name;
        $fs_errorMsg .= $upload_image4->warning_error;
    }

    if ($filename4 != "") {
        $$fs_fieldupload4 = $filename4;
        $myform->add($fs_fieldupload4, $fs_fieldupload4, 0, 1, "", 0, "", 0, "");
    }//End if($filename2 != "")
		if($fs_errorMsg == "") {

			//Insert to database
			$myform->removeHTML(0);

			$db_update = new db_execute($myform->generate_update_SQL($id_field, $record_id));
			unset($db_update);

			redirect($fs_redirect);

		}//End if($fs_errorMsg == "")

	}//End if($action ==1 "insert")

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?=$load_header?>
<?
$myform->checkjavascript();
//chuyển các trường thành biến để lấy giá trị thay cho dùng kiểu getValue
$myform->evaluate();
$fs_errorMsg .= $myform->strErrorField;
?>
</head>
<body topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
<? /*------------------------------------------------------------------------------------------------*/ ?>
<?=template_top($fs_title)?>
	<p align="center" style="padding-left:10px;">
	<?
	$form = new form();
	$form->create_form("edit", $fs_action, "post", "multipart/form-data",'onsubmit="validateForm(); return false;"');
	$form->create_table();
	?>
	<?=$form->text_note('Những ô có dấu sao (<font class="form_asterisk">*</font>) là bắt buộc phải nhập.')?>
	<?=$form->errorMsg($fs_errorMsg)?>
	<?=$form->text("Admin email", "con_admin_email", "con_admin_email", $con_admin_email, "Admin email", 1, 200, "", 255, "", "", "")?>
	<?=$form->text("Tiêu đề Website", "con_site_title", "con_site_title", $con_site_title, "Tiêu đề Website", 1, 350, "", 255, "", "", "")?>
	<?=$form->textarea("Meta Keyword", "con_meta_keywords", "con_meta_keywords", $con_meta_keywords, "Meta Keyword", 0, 350, 75, "", "", "")?>
	<?=$form->textarea("Meta Description", "con_meta_description", "con_meta_description", $con_meta_description, "Meta Description", 0, 350, 100, "", "", "")?>
	<?=$form->textarea("Page Facebook", "con_page_fb", "con_page_fb", $con_page_fb, "Page Facebook", 0, 350, 100, "", "", "")?>
	<?=$form->text("Số hotline", "con_hotline", "con_hotline", $con_hotline, "hotline", 1, 250, "", 250, "", "", "&nbsp(Gồm các số điện thoại cách nhau bởi dấu \"|\")")?>
	<?=$form->text("Địa chỉ", "con_address", "con_address", $con_address, "address", 1, 250, "", 250, "", "")?>
	<?=$form->text("Link facebook", "con_link_fb", "con_link_fb", $con_link_fb, "Link facebook", 0, 250, "", 250, "", "")?>
	<?=$form->text("Link twiter", "con_link_twiter", "con_link_twiter", $con_link_twiter, "Link twiter", 0, 250, "", 250, "", "")?>
	<?=$form->text("Link instar", "con_link_insta", "con_link_insta", $con_link_insta, "Link instar", 0, 250, "", 250, "", "")?>
    <?= $form->getFile("Logo top", "con_logo_top", "con_logo_top", "Logo top", 1, 32, "", "") ?>
    <tr id="con_logo_top">
        <td class="form_name"></td>
        <td class="form_text">
            <? if ($con_logo_top != "") { ?>
                <img width="185px" src="../../../data/background/<?= $con_logo_top ?>"/>
                <a href="javascript:;" onclick="delete_background(1)">Xóa</a>
            <? } ?>
        </td>
    </tr>
    <?= $form->getFile("Logo bottom", "con_logo_bottom", "con_logo_bottom", "Logo bottom", 1, 32, "", "") ?>
    <tr id="con_logo_bottom">
        <td class="form_name"></td>
        <td class="form_text">
            <? if ($con_logo_bottom != "") { ?>
                <img width="185px" src="../../../data/background/<?= $con_logo_bottom ?>"/>
                <a href="javascript:;" onclick="delete_background(1)">Xóa</a>
            <? } ?>
        </td>
    </tr>
    <?=$form->textarea("Map", "con_map", "con_map", $con_map, "Map", 0, 350, 100, "", "", "")?>
    <?=$form->close_table();?>
	<?=$form->wysiwyg("Nội dung footer", "con_footer", $con_footer, $wys_path, "99%", 250)?>
	<?=$form->wysiwyg("Liên hệ", "con_content_ship", $con_content_ship, $wys_path, "99%", 250)?>
	<?=$form->create_table();?>
	<?=$form->button("submit" . $form->ec . "reset", "submit" . $form->ec . "reset", "submit" . $form->ec . "reset", "Cập nhật" . $form->ec . "Làm lại", "Cập nhật" . $form->ec . "Làm lại", $form->ec, "");?>
    <?=$form->hidden("action", "action", "execute", "");?>
	<?
	$form->close_form();
	unset($form);
	?>
	</p>
<?=template_bottom() ?>
<? /*------------------------------------------------------------------------------------------------*/ ?>
</body>
</html>
<script type="text/javascript">
	function delete_background(id){
		$.post("delete_background.php",{
			id:id
		}, function(json){
			if(json.code == 1){
				alert("Xóa thành công");
				if (id == 1) {
					$("#con_background_img").html("");
				}else if(id == 2){
					$("#con_background_homepage").html("");
				};
			}else{
				alert("Xảy ra lỗi khi xóa");
			}
		}, 'json')
	}
</script>