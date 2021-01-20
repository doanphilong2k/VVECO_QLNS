<?
require_once("../../resource/security/security.php");

$module_id = 11;
$fs_fieldupload      = "con_background_img";
$fs_fieldupload2		= "con_background_homepage";
$fs_fieldupload3		= "con_logo_top";
$fs_fieldupload4		= "con_logo_bottom";

$fs_filepath			= "../../../data/background/";
$fs_extension			= "gif,jpg,jpe,jpeg,png";
$fs_filesize			= 400;
//Check user login...
checkLogged();
//Check access module...
if(checkAccessModule($module_id) != 1) redirect($fs_denypath);

//Declare prameter when insert data
$fs_table				= "configuration";
$id_field				= "con_id";

//Cấu hình static
$arrStatic	= array ("Liên hệ"					=> "con_static_contact",
							"Cuối trang"				=> "con_static_footer",
							"Đăng nhập để đặt hàng"	=> "con_static_paymentlogin",
							"Thông báo ở phần toolbar dưới"	=> "con_static_intro",
							"Hướng dẫn sử dụng"		=> "con_static_huongdansudung",
							"Yêu cầu dịch vụ"			=> "con_static_yeucaudichvu",
							);
?>