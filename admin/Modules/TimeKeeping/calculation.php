<?php
require_once("inc_security.php");
require_once("Excel.php");

$fs_title    = "Checkin";
$fs_action   = "listing.php" . getURL(0, 0, 0, 1, "record_id");
$fs_redirect = "listing.php" . getURL(0, 0, 0, 1, "record_id");
$fs_errorMsg = "";
$mydate      = getdate(date(time()));
$month       = 7;
$year        = 2020;

$id = getValue("id", "str", "GET", "");
// echo $id;
$member_id = getValue("member_id", "int", "GET", "");
$name = getValue("name", "str", "GET", "");
$start_date = getValue("start_date", "str", "GET", "");
$finish_date = getvalue("finish_date", "str", "GET", "");
$total_time = getValue("total_time", "str", "GET", "");
$NoData = "";
$idCheckin = "";
$idCheckout = "";


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

//checkin query
$sqlWhere = "";
$sqlWhereTotaltimeCheckin = "";
$sqlWhereTotaltimeCheckout = "";
$sqlQuery_checkin_select = " SELECT member_checkin.id,member_id, members.name, members.avatar, member_checkin.image, member_checkin.checkin_time
                            FROM member_checkin, members ";
$sqlQuery_checkin_where  = " WHERE MONTH(member_checkin.checkin_time)= 7 AND YEAR(member_checkin.checkin_time) = 2020
                                AND member_checkin.member_id = members.id
                                AND members.active = 1 AND member_checkin.active = 1 ";
$sqlQuery_checkin_groupby = " GROUP BY DATE(member_checkin.checkin_time), member_id ";

//checkout query
$sqlQuery_checkout_select = " SELECT member_checkin.id, member_checkin.member_id, members.name, member_checkin.checkin_time as checkout_time 
                             FROM member_checkin, members ";
$sqlQuery_checkout_where  = " WHERE member_checkin.member_id = members.id	
                                AND member_checkin.id IN (SELECT MAX(member_checkin.id) 
                                                            FROM member_checkin, members"
    . $sqlQuery_checkin_where .
    " GROUP BY DATE(checkin_time), member_id) ";

//Searching
if (isset($member_id) && is_numeric($member_id) && $member_id > 0) {
    $sqlWhere .= " AND member_id = " . $member_id;
}
if (isset($name) && $name != "") {
    $sqlWhere .= " AND members.name LIKE '%" . $name . "%'";
}
if ($start_date != "" && $finish_date == "") {
    ?>

    <div id="finish-alert">
        <span class="glyphicon glyphicon-exclamation-sign" style="color: rgb(255, 153, 0); margin-right: 3px; font-size:15px"></span>
        Vui lòng nhập Ngày kết thúc.
        <span class="arrow-up"></span>
        <span id="finish-remove" class="glyphicon glyphicon-remove"></span>
    </div>

    <?
}
if ($start_date == "" && $finish_date != "") {
    ?>

    <div id="start-alert">
        <span class="glyphicon glyphicon-exclamation-sign" style="color: rgb(255, 153, 0); margin-right: 3px; font-size:15px"></span>
        Vui lòng nhập Ngày bắt đầu.
        <span class="arrow-up"></span>
        <span id="start-remove" class="glyphicon glyphicon-remove"></span>
    </div>

    <?
}
if (isset($start_date) && isset($finish_date)) {
    if (strtotime($start_date) && strtotime($finish_date)) {
        $sqlQuery_checkin_where  = " WHERE member_checkin.checkin_time BETWEEN '" . $start_date . " 00:00:00' 
                                        AND '" . $finish_date . " 23:59:59'
                                        AND member_checkin.member_id = members.id
                                        AND members.active = 1 AND member_checkin.active = 1";
        $sqlQuery_checkout_where  = " WHERE member_checkin.member_id = members.id	
                                AND member_checkin.id IN (SELECT MAX(member_checkin.id) 
                                                            FROM member_checkin, members"
            . $sqlQuery_checkin_where .
            " GROUP BY DATE(checkin_time), member_id) ";
    }
}

if(isset($total_time) && $total_time != 0){
    $sqlQuery_checkin  = $sqlQuery_checkin_select  .$sqlQuery_checkin_where  .$sqlWhere .$sqlQuery_checkin_groupby;
    $sqlQuery_checkout = $sqlQuery_checkout_select .$sqlQuery_checkout_where .$sqlWhere;

    $db_listing = new db_query($sqlQuery_checkin);
    $db_checkout = new db_query($sqlQuery_checkout);

    while ($list_checkin = mysqli_fetch_assoc($db_listing->result)) {
        $list_checkout = mysqli_fetch_array($db_checkout->result);

        $startTime = new DateTime($list_checkin["checkin_time"]);
        $finishTime = new DateTime($list_checkout["checkout_time"]);
        $diff = $finishTime->diff($startTime);

        if(($diff->format("%s") + $diff->format("%i")*60 + $diff->format("%h")*60*60) >= ($total_time*60*60)
            && ($diff->format("%s") + $diff->format("%i")*60 + $diff->format("%h")*60*60) <=(($total_time+1)*60*60)){
            if($idCheckin == "" && $idCheckout == ""){
                $idCheckin  .= $list_checkin["id"];
                $idCheckout .= $list_checkout["id"];
            }
            else{
                $idCheckin  .= "," . $list_checkin["id"];
                $idCheckout .= "," . $list_checkout["id"];
            }
        }
    }
    $sqlWhereTotaltimeCheckin .= " AND member_checkin.id IN (".$idCheckin.")";
    $sqlWhereTotaltimeCheckout .= " AND member_checkin.id IN (".$idCheckout.")";
}

$db_count = new db_query($sqlQuery_checkin_select  .$sqlQuery_checkin_where  .$sqlWhere .$sqlWhereTotaltimeCheckin .$sqlQuery_checkin_groupby );


//	LEFT JOIN users ON(uso_user_id = use_id)
$total_record = 0;
while ($listing_count = mysqli_fetch_assoc($db_count->result)) {
    $total_record++;
}
$current_page = getValue("page", "int", "GET", 1);
if ($total_record % $page_size == 0) $num_of_page = $total_record / $page_size;
else $num_of_page = (int)($total_record / $page_size) + 1;
if ($current_page > $num_of_page) $current_page = $num_of_page;
if ($current_page < 1) $current_page = 1;
unset($db_count);
//End get page break params


$sqlQuery_checkin_limit = " LIMIT " . ($current_page - 1) * $page_size . "," . $page_size;

$sqlQuery_checkout_limit = " LIMIT " . ($current_page - 1) * $page_size . "," . $page_size;

$sqlQuery_checkin  = $sqlQuery_checkin_select  .$sqlQuery_checkin_where  .$sqlWhere .$sqlWhereTotaltimeCheckin  .$sqlQuery_checkin_groupby .$sqlQuery_checkin_limit;
$sqlQuery_checkout = $sqlQuery_checkout_select .$sqlQuery_checkout_where .$sqlWhere .$sqlWhereTotaltimeCheckout .$sqlQuery_checkout_limit;



if ($NoData == "") {
    $db_listing = new db_query($sqlQuery_checkin);
    $db_checkout = new db_query($sqlQuery_checkout);
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <?= $load_header ?>
    <script language="javascript" src="../../resource/js/grid.js"></script>
</head>

<body style="font-size: 11px !important;" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
    <div id="show"></div>
    <? /*---------Body------------*/ ?>
    <div class="listing">
        <div class="header">
            <h3>Danh sách Checkin</h3>

            <div class="search" style="width: 99.5%">
                <form action="calculation.php" method="get" name="form_search" onsubmit="check_form_submit(this); return false">
                    <input type="hidden" name="search" id="search" value="1">
                    <table cellpadding="0" cellspacing="0" border="0" style="width: 100%">
                        <tbody>
                            <tr>
                                <td class="text">Mã nhân viên</td>
                                <td><input type="text" class="form-control" name="member_id" id="member_id" value="<?= $member_id ?>" placeholder="Mã nhân viên" style="width: 200px" /></td>
                                <td class="text">Họ và tên</td>
                                <td><input type="text" class="form-control" name="name" id="name" value="<?= $name ?>" placeholder="Họ và tên" style="width: 200px" /></td>
                                <td class="text">Tổng Thời Gian</td>
                                <td><input type="number" id="time-total" class="form-control" name="total_time" value="<?= $total_time ?>" placeholder="Tổng thời gian" style="width: 200px" /></td>
                            </tr>
                            <tr>
                                <td class="text">Ngày bắt đầu</td>
                                <td><input type="date" class="form-control" name="start_date" id="checkin_time" value="<?= $start_date ?>" placeholder="Thời gian checkin" style="width: 200px" /></td>
                                <td class="text">Ngày kết thúc</td>
                                <td><input type="date" class="form-control" name="finish_date" id="checkout_time" value="<?= $finish_date ?>" placeholder="Thời gian checkout" style="width: 200px" /></td>
                                <td colspan="2">&nbsp;<input type="submit" class="btn btn-sm btn-info" value="Tìm kiếm" style="float: left; margin-left:25px"></td>
                            </tr>
                        </tbody>
                    </table>
                </form>

                <script type="text/javascript">
                    function check_form_submit(obj) {
                        document.form_search.submit();
                    };
                </script>
            </div>

            <div style="padding: 0px 0px 5px 5px; margin-top: 6px; margin-bottom: -5px">
                <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#form_export"><i class="fa fa-file-excel-o"></i> Xuất Excel Danh sách Checkin</button>
                <button type="button" class="btn btn-xs btn-success" data-toggle="modal" data-target="#form_import"><i class="fa fa-file-excel-o"></i> Nhập Danh sách Checkin từ Excel</button>
                <a class="btn btn-xs btn-link" href="/data/excels/import_users_from_excel_example.xlsx"><i class="fa fa-download" aria-hidden="true"></i> Tải về file Excel mẫu</a>
            </div>
        </div>

        <div class="content">
            <div class="table-container">
                <div style="clear: both;"></div>
                <table cellpadding="5" cellspacing="0" class="table table-hover table-bordered table-sticky" width="100%">
                    <tr class="warning stick">
                        <td class="h" width="40" style="text-align: center">STT</td>
                        <!--                    <td width="50" class="h check">-->
                        <!--                        <input type="checkbox" id="check_all" onclick="checkall(-->
                        <?//= $num_row ?>
                        <!--)">-->
                        <!--                    </td>-->
                        <td class="h">Mã nhân viên</td>
                        <td class="h">Họ và Tên</td>
                        <td class="h">Avatar</td>
                        <td class="h">Image</td>
                        <td class="h">Thời gian Checkin</td>
                        <td class="h">Thời gian Checkout</td>
                        <td class="h">Tổng thời gian</td>
                    </tr>
                    <?
                //Đếm số thứ tự
                $No = ($current_page - 1) * $page_size;
                
                if(isset($NoData) && $NoData != "" ){
                    ?>
                    <tr>
                        <td cols></td>
                    </tr>
                    <?
                }
                else{
                    while ($listing = mysqli_fetch_assoc($db_listing->result)) {
                        $list_checkout = mysqli_fetch_array($db_checkout->result);

                        $startTime = new DateTime($listing["checkin_time"]);
                        $finishTime = new DateTime($list_checkout["checkout_time"]);
                        $diff = $finishTime->diff($startTime);

                        $No++;
                        ?>
                        <tr id="tr_<?= $listing["id"] ?>">
                            <td width="40" style="text-align:center"><span style="color:#142E62; font-weight:bold"><?= $No ?></span></td>
                            <td>
                                <? echo $listing["member_id"] ?>
                            </td>
                            <td>
                                <? echo $listing["name"] ?>
                            </td>
                            <td>
                                <img src="<? echo $listing["avatar"] ?>" alt="avatar">
                            </td>
                            <td>
                                <img src="<? echo $listing["image"] ?>" alt="image">
                            </td>
                            <td>
                                <? echo $listing["checkin_time"] ?>
                            </td>
                            <td><? echo $list_checkout["checkout_time"] ?></td>
                            <td><?
                                print($diff->format("%H:%I:%S"));
                                ?></td>
                        </tr>
                        <?
                     }
                } ?>


                </table>
            </div>
        </div>

        <div class="footer">
            <table cellpadding="5" cellspacing="0" width="100%" class="page_break">
                <tbody>
                    <tr>
                        <!--                <td width="150">-->
                        <!--                    <button class="btn btn-sm btn-primary"-->
                        <!--                            onclick="if (confirm('Bạn có chắc chắn muốn duyệt ảnh cho những người dùng đã chọn ?')){ approveAll(-->
                        <?//= $total_record ?>
                        <!--); }">-->
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
                success: function(data) {
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

    $(document).ready(function() {
        var time = 1;
        if (time == 1) {
            $("#time-total").attr('type', 'time');
        }
    });

    $(document).ready(function(){
        var blank = "../../../images/blank-photo.png";
        if ( $(".avatar-img img").attr('src') == '')
        {
            $(".avatar-img img").attr('src', blank );
        }
    });

    // function alertRemove(){
    //     var alert = document.getElementById("date-alert");
    //     alert.style.display = "none";
    // }

    const finish = document.getElementById("finish-alert");
    const finish_remove = document.getElementById("finish-remove");
    const start = document.getElementById("start-alert");
    const start_remove = document.getElementById("start-remove");

    finish_remove.addEventListener('click', () => finish.style.opacity = '0');
    finish.addEventListener('transitionend', () => finish.remove());
    start_remove.addEventListener('click', () => start.style.opacity = '0');
    start.addEventListener('transitionend', () => start.remove());

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

    /* input[type=time]::-webkit-datetime-edit-fields-wrapper {
        display: flex;
    } */

    input[type=time]::-webkit-datetime-edit-text {
        padding: 2px 5px;
    }

    .avatar-img {
        width: 80px;
        height: 80px;
        overflow: hidden;
        margin: 1px auto;
    }

    .avatar-img img {
        width: 100%;
        height: auto;
    }

    .table-container {
        width: 99.5%;
        height: 373px;
        overflow-y: auto;
    }

    .table-sticky {
        position: relative;
    }

    table.table-sticky tr.stick td {
        position: sticky;
        top: -1px;
    }

    #finish-alert,
    #start-alert {
        border: 1px solid grey;
        border-radius: 6px;
        background-color: white;
        padding: 13px 10px;
        position: absolute;
        box-shadow: 3px 3px 4px 0 rgba(0,0,0,0.3);
        left: 480px;
        margin-top: 102px;
        z-index: 1000;
        font-size: 13px;
        transition: opacity 0.5s;
    }

    #start-alert {
        left: 100px;
    }

    .arrow-up {
        width: 0;
        height: 0;
        position: relative;
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-bottom: 10px solid grey;
        top: -40px;
        left: -180px
    }

    .arrow-up::before {
        content: "";
        width: 0;
        height: 0;
        position: absolute;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-bottom: 9px solid white;
        top: 15px;
        left: -6px;
    }

    #finish-remove,
    #start-remove {
        margin-left: -10px;
        cursor: pointer;
    }


</style>