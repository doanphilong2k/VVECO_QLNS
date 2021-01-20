<?
/*
Thực hiện xóa bground toàn trang và trang chủ
	- id = 1 => Xóa bg chung
	- id = 2 => Xóa bg trang chủ
*/
include("inc_security.php");
$type_bg = getValue("id","int","POST",0);
$json				= array();
$sql_field		= '' ;

if($is_admin != 1){
	$json['code'] 	= 0 ;
	$json['msg']	= "Bạn không có quyền này";
	die(json_encode($json));

}else{
	if($type_bg == 0){
		$json['code'] = 0;
		$json['msg']  = "xảy ra lỗi khi xóa background"	; 

	}else{
		if($type_bg == 1){
			$sql_field  = "con_background_img";
		}
		if($type_bg == 2){
			$sql_field = "con_background_homepage";
		}

		$query  = new db_execute("UPDATE " . $fs_table . " SET " . $sql_field . " = ''", 1);
		if($query->row_affect > 0){
			$json['code'] = 1;
		}else{
			$json['code'] = 0;	
			$json['msg']  = "xảy ra lỗi khi xóa background"	; 
		}	
	}
}
die(json_encode($json));
?>