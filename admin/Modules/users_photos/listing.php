<?
require_once("inc_security.php");
//Khai báo biến khi hiển thị danh sách
$fs_title = "Duyệt Ảnh";
$fs_action = "listing.php" . getURL(0, 0, 0, 1, "record_id");
$fs_redirect = "listing.php" . getURL(0, 0, 0, 1, "record_id");
$fs_errorMsg = "";

$use_school_id = getValue("use_school_id", "int", "GET", 0);
$use_faculty_id = getValue("use_faculty_id", "int", "GET", 0);
$use_class_id = getValue("use_class_id", "int", "GET", 0);
$keyword = getValue("keyword", "str", "GET", "");
$code = getValue("code", "str", "GET", "");
$id_number = getValue("id_number", "str", "GET", "");

// Query danh sách Trường
$list_schools = new db_query("SELECT * FROM schools WHERE sch_active = 1");
$arrTmp = convert_result_set_2_array($list_schools->result, "sch_id");
$arrSchools = array(0 => "- Tất cả các Trường -");
foreach ($arrTmp as $school) {
    $arrSchools[$school["sch_id"]] = $school["sch_name"];
}
// Query danh sách Khoa theo Trường được chọn
$sql_faculties = "";
if ($use_school_id > 0) $sql_faculties .= " AND fac_school_id = " . $use_school_id;
$list_faculties = new db_query("SELECT * FROM faculties WHERE fac_active = 1" . $sql_faculties);
$arrTmp = convert_result_set_2_array($list_faculties->result, "fac_id");
$arrFaculties = array(0 => "- Tất cả các Khoa -");
foreach ($arrTmp as $faculty) {
    $arrFaculties[$faculty["fac_id"]] = $faculty["fac_name"];
}

// Query danh sách Lớp theo Khoa được chọn
$sql_classes = "";
if ($use_school_id > 0) $sql_classes .= " AND cls_school_id = " . $use_school_id;
if ($use_faculty_id > 0) $sql_classes .= " AND cls_faculty_id = " . $use_faculty_id;
$list_classes = new db_query("SELECT * FROM classes WHERE cls_active = 1" . $sql_classes);
$arrTmp = convert_result_set_2_array($list_classes->result, "cls_id");
$arrClasses = array(0 => "- Tất cả các Lớp -");
foreach ($arrTmp as $class) {
    $arrClasses[$class["cls_id"]] = $class["cls_name"];
}

$sqlWhere = "";

//Tìm theo ID
if ($use_school_id > 0) {
    $sqlWhere .= " AND use_school_id = " . $use_school_id;
}
if ($use_faculty_id > 0) {
    $sqlWhere .= " AND use_faculty_id = " . $use_faculty_id;
}
if ($use_class_id > 0) {
    $sqlWhere .= " AND use_class_id = " . $use_class_id;
}
//Tìm theo keyword
if ($keyword != "") {
    $sqlWhere .= " AND use_name LIKE '%" . $keyword . "%'";
}
//Tìm theo keyword
if ($code != "") {
    $sqlWhere .= " AND use_code_md5 = '" . md5($code) . "'";
}
//Tìm theo keyword
if ($id_number != "") {
    $sqlWhere .= " AND use_idnumber_md5 = '" . md5($id_number) . "'";
}

//Sort data
$sort = getValue("sort");
switch ($sort) {
    default:
        $sqlOrderBy = "use_id DESC";
        break;
}


//Get page break params
$page_size = 30;
$page_prefix = "Trang: ";
$normal_class = "page";
$selected_class = "page_current";
$previous = '<img align="absmiddle" border="0" src="../../resource/images/grid/prev.gif">';
$next = '<img align="absmiddle" border="0" src="../../resource/images/grid/next.gif">';
$first = '<img align="absmiddle" border="0" src="../../resource/images/grid/first.gif">';
$last = '<img align="absmiddle" border="0" src="../../resource/images/grid/last.gif">';
$break_type = 1; //"1 => << < 1 2 [3] 4 5 > >>", "2 => < 1 2 [3] 4 5 >", "3 => 1 2 [3] 4 5", "4 => < >"
$url = getURL(0, 0, 1, 1, "page");

$db_count = new db_query("SELECT COUNT(*) AS count
                          FROM users
                          WHERE 1 " . $sqlWhere);

//	LEFT JOIN users ON(uso_user_id = use_id)
$listing_count = mysqli_fetch_assoc($db_count->result);
$total_record = $listing_count["count"];
$current_page = getValue("page", "int", "GET", 1);
if ($total_record % $page_size == 0) $num_of_page = $total_record / $page_size;
else $num_of_page = (int)($total_record / $page_size) + 1;
if ($current_page > $num_of_page) $current_page = $num_of_page;
if ($current_page < 1) $current_page = 1;
unset($db_count);
//End get page break params

$db_listing = new db_query("SELECT *
                            FROM users
                            WHERE 1 " . $sqlWhere . "
                            ORDER BY " . $sqlOrderBy . "
                            LIMIT " . ($current_page - 1) * $page_size . "," . $page_size);
$num_row = mysqli_num_rows($db_listing->result);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <?= $load_header ?>
    <script language="javascript" src="../../resource/js/grid.js"></script>
</head>
<body style="font-size: 11px !important;" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
<div id="show"></div>
<? /*---------Body------------*/ ?>
<div class="listing">
    <div class="header">
        <h3>Duyệt ảnh người dùng</h3>

        <div class="search">
            <form action="listing.php" methor="get" name="form_search" onsubmit="check_form_submit(this); return false">
                <input type="hidden" name="search" id="search" value="1">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tbody>
                    <tr>
                        <td class="text">Trường</td>
                        <td>
                            <select class="form-control" name="use_school_id" id="use_school_id" style="width: 200px">
                                <?
                                foreach ($arrSchools as $key => $value) {
                                    $selected = ($key == $use_school_id ? " selected" : "");
                                    echo '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
                                }
                                ?>
                            </select>
                        </td>

                        <td class="text">Khoa</td>
                        <td>
                            <select class="form-control" name="use_faculty_id" id="use_faculty_id" style="width: 200px">
                                <?
                                foreach ($arrFaculties as $key => $value) {
                                    $selected = ($key == $use_faculty_id ? " selected" : "");
                                    echo '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
                                }
                                ?>
                            </select>
                        </td>

                        <td class="text">Lớp</td>
                        <td>
                            <select class="form-control" name="use_class_id" id="use_class_id" style="width: 200px">
                                <?
                                foreach ($arrClasses as $key => $value) {
                                    $selected = ($key == $use_class_id ? " selected" : "");
                                    echo '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="text">Sinh viên</td>
                        <td><input type="text" class="form-control" name="keyword" id="keyword" value="<?= $keyword ?>"
                                   placeholder="Tên Sinh viên" style="width: 200px"/></td>
                        <td class="text">Mã SV</td>
                        <td><input type="text" class="form-control" name="code" id="code" value="<?= $code ?>"
                                   placeholder="Mã Sinh viên" style="width: 200px"/></td>
                        <td class="text">CMND/Hộ chiếu</td>
                        <td><input type="text" class="form-control" name="id_number" id="id_number"
                                   value="<?= $id_number ?>" placeholder="Số CMND/Hộ chiếu" style="width: 200px"/></td>
                        <td>&nbsp;<input type="submit" class="btn btn-sm btn-info" value="Tìm kiếm"></td>
                    </tr>
                    </tbody>
                </table>
            </form>
            <script type="text/javascript">
                function check_form_submit(obj) {
                    document.form_search.submit();
                }
                ;
            </script>
        </div>
    </div>

    <div class="content">
        <div>
            <div style="clear: both;"></div>
            <table cellpadding="5" cellspacing="0" class="table table-hover table-bordered" width="100%">
                <tr class="warning">
                    <td class="h" width="40" style="text-align: center">STT</td>
<!--                    <td width="50" class="h check">-->
<!--                        <input type="checkbox" id="check_all" onclick="checkall(--><?//= $num_row ?><!--)">-->
<!--                    </td>-->
                    <td class="h">Trường</td>
                    <td class="h">Khoa</td>
                    <td class="h">Lớp</td>
                    <td class="h">Thông tin Người dùng</td>
                    <td class="h" width="100">Trạng thái</td>
                </tr>
                <?
                //Đếm số thứ tự
                $No = ($current_page - 1) * $page_size;
                while ($listing = mysqli_fetch_assoc($db_listing->result)) {
                    $No++;
                    ?>
                    <tr id="tr_<?= $listing["use_id"] ?>">
                        <td width="40" style="text-align:center"><span
                                style="color:#142E62; font-weight:bold"><?= $No ?></span></td>
<!--                        <td class="check" style="text-align: center;"><input type="checkbox" class="check"-->
<!--                                                                             name="record_id[]" id="record_--><?//= $No ?><!--"-->
<!--                                                                             value="--><?//= $listing["use_id"] ?><!--"></td>-->
                        <td><?= showUserSchool($listing["use_school_id"]) ?></td>
                        <td><?= showUserFaculty($listing["use_faculty_id"]) ?></td>
                        <td><?= showUserClass($listing["use_class_id"]) ?></td>
                        <td>
                            <div><b>Họ và tên:</b> <?= $listing["use_name"] ?></div>
                            <div><b>Mã SV:</b> <?= $listing["use_code"] ?></div>
                            <div><b>Số CMT/Hộ chiếu:</b> <?= $listing["use_idnumber"] ?></div>
                            <div><b>Ngày sinh:</b> <?= date("d/m/Y", $listing["use_birthdays"]) ?></div>
                        </td>
                        <td style="vertical-align: middle; text-align: center">
                            <?
                            if ($listing['use_approved_image'] == 1) {
                                echo '<span class="label label-success">Đã duyệt</span>';
                            } else {
                                echo '<span class="label label-warning">Chưa duyệt</span>';
                            }
                            ?>

                            <?
                            if ($listing['use_approved_image'] == 0) {
                                ?>
                                <a href="detail.php?record_id=<?= $listing['use_id'] ?>" class="btn btn-xs btn-primary"
                                   style="margin-top: 10px">
                                    <i class="fa fa-check" aria-hidden="true"></i> Duyệt ảnh
                                </a>
                            <?
                            }else{
                            ?>
                                <a href="detail.php?record_id=<?= $listing['use_id'] ?>" class="btn btn-xs btn-primary"
                                   style="margin-top: 10px">
                                    <i class="fa fa-eye" aria-hidden="true"></i> Xem ảnh
                                </a>
                            <?
                            }
                            ?>
                        </td>
                    </tr>
                <? } ?>
            </table>
        </div>
    </div>

    <div class="footer">
        <table cellpadding="5" cellspacing="0" width="100%" class="page_break">
            <tbody>
            <tr>
<!--                <td width="150">-->
<!--                    <button class="btn btn-sm btn-primary"-->
<!--                            onclick="if (confirm('Bạn có chắc chắn muốn duyệt ảnh cho những người dùng đã chọn ?')){ approveAll(--><?//= $total_record ?><!--); }">-->
<!--                        <i class="fa fa-check-square-o" aria-hidden="true"></i> Duyệt tất cả-->
<!--                    </button>-->
<!--                </td>-->
                <td width="150">Tổng số bản ghi : <span id="total_footer"><?= formatCurrency($total_record) ?></span>
                </td>
                <td>
                    <?
                    if ($total_record > $page_size) {
                        echo generatePageBar($page_prefix, $current_page, $page_size, $total_record, $url, $normal_class, $selected_class, $previous, $next, $first, $last, $break_type, 0, 15);
                    }
                    ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<? /*---------Body------------*/ ?>
</body>
</html>
<script type="text/javascript">
    function approveAll(total) {
        var total_footer = document.getElementById("total_footer").innerHTML;
        var listid = '0';
        var selected = false;
        for (i = 1; i <= total; i++) {
            if (document.getElementById("record_" + i).checked == true) {
                id = document.getElementById("record_" + i).value;
                listid += ',' + id;
                total_footer = total_footer - 1;
                selected = true;
            }
        }

        if (selected === true) {
            $.ajax({
                type: "POST",
                url: "update_status.php",
                data: "record_id=" + listid,
                success: function (data) {
                    alert(data.msg);
                    if (parseInt(data.status) == 1) {
                        for (i = 1; i <= total; i++) {
                            if (document.getElementById("record_" + i).checked == true) {
                                id = document.getElementById("record_" + i).value;
                                $("#tr_" + id + " td:last").html('<span class="label label-success">Đã duyệt</span>');
                            }
                        }
                    }
                },
                dataType: "json"
            });
        }
    }
</script>

<style type="text/css">
    .page {
        padding: 2px;
        font-weight: bold;
        color: #333333;
    }

    .page_current {
        padding: 2px;
        font-weight: bold;
        color: red;
    }
</style>