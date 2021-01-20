<?
include("inc_security.php");

$fs_redirect = getValue("url", "str", "GET", base64_encode("listing.php"));
$record_id = getValue("record_id");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <?= $load_header ?>
    <style type="text/css">
        .timeline {
            background: #FFF;
            width: 750px;
            margin: 20px auto;
            padding: 0;
            position: relative;
            padding: 30px 0px;
            border-radius: 5px;
        }

        .timeline::before {
            border-radius: .25rem;
            background: #dee2e6;
            bottom: 0;
            content: '';
            left: 31px;
            margin: 0;
            position: absolute;
            top: 0;
            width: 4px;
        }

        .timeline > div {
            margin-bottom: 15px;
            margin-right: 10px;
            position: relative;
        }

        .timeline > div::after, .timeline > div::before {
            content: "";
            display: table;
        }

        .timeline > div > .timeline-item {
            box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
            border-radius: .25rem;
            background: #fff;
            color: #495057;
            margin-left: 60px;
            margin-right: 15px;
            margin-top: 0;
            padding: 0;
            position: relative;
        }

        .timeline-inverse > div > .timeline-item {
            box-shadow: none;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        .timeline > div::after, .timeline > div::before {
            content: "";
            display: table;
        }

        .timeline > div > .timeline-item > .timeline-header {
            border-bottom: 1px solid rgba(0, 0, 0, .125);
            color: #495057;
            font-size: 13px !important;
            line-height: 180%;
            margin: 0;
            padding: 10px;
        }

        .timeline > div > .timeline-item > .timeline-body, .timeline > div > .timeline-item > .timeline-footer {
            padding: 10px;
        }

        .timeline .image img {
            max-width: 100%;
            max-height: 100%;
        }

        .timeline .bg-blue {
            background: #008dca;
            border-radius: 50%;
            font-size: 15px;
            height: 30px;
            left: 18px;
            line-height: 30px;
            position: absolute;
            text-align: center;
            top: 0;
            width: 30px;
            color: #fff !important;
        }

        .user_picture, .demo_picture {
            width: 150px;
            height: 200px;
            margin: auto;
            position: relative;
            background: #FFF;
            border: 1px solid #dde2e6;
        }

        .user_picture .image, .demo_picture .image {
            width: 100%;
            height: 100%;
            overflow: hidden;
            text-align: center;
        }

        .user_picture img {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            margin: auto;
            max-width: 100%;
            max-height: 100%;
        }

    </style>
</head>
<body topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
<? /*---------Body------------*/ ?>
<div class="listing" style="padding: 0px 20px; background-color: #f3f6f8; border: 1px solid #dce5eb">
    <div id="timeline">
        <!-- The timeline -->
        <div class="timeline timeline-inverse">
            <div class="title" style="text-align: center;"><b style="font-size: 20px">DUYỆT ẢNH NGƯỜI DÙNG</b></div>
            <?
            $db_question = new db_query("SELECT *
                                         FROM questions
                                         LEFT JOIN users_photos ON(up_user_id = " . $record_id . " AND up_question_id = que_id)
                                         WHERE que_active = 1
                                         ORDER BY que_stt ASC");
            $stt = 0;
            while ($rowQuestion = mysqli_fetch_assoc($db_question->result)) {
                $stt++;
                $pictureDemoURL = "/data/questions/" . $rowQuestion["que_img_example"];
                ?>
                <!-- timeline item -->
                <div>
                    <i class="fa fa-camera bg-blue"></i>

                    <div class="timeline-item">
                        <div class="timeline-header">
                            <strong style="color: #007bff;">
                                Kiểu ảnh <?=$stt?>
                            </strong><? if ($rowQuestion["que_required"] == 1) { ?> (<span class="text-danger">*</span>)<? } ?>: <?= $rowQuestion["que_content"] ?>
                        </div>

                        <div class="timeline-body row">
                            <div class="col-md-6">
                                <div class="demo_picture">
                                    <div class="image">
                                        <img id="demo_<?= $rowQuestion["que_id"] ?>" src="<?= $pictureDemoURL ?>">
                                    </div>
                                </div>
                                <div class="name" style="padding: 10px; text-align: center">Ảnh mẫu</div>
                            </div>
                            <div class="col-md-6">
                                <div class="user_picture">
                                    <div class="image">
                                        <?
                                        $pictureURL = "/images/photo.png";
                                        if ($rowQuestion["up_picture"] != "") {
                                            $uPath = str_pad(intval($rowQuestion["up_user_id"]), 2, '0', STR_PAD_LEFT);
                                            $pictureURL = "/data/users/" . $uPath . "/" . $rowQuestion["up_picture"];
                                        }
                                        ?>
                                        <img id="img_<?= $rowQuestion["que_id"] ?>" src="<?= $pictureURL ?>">
                                    </div>
                                </div>
                                <div class="name" style="padding: 10px; text-align: center">Ảnh người dùng tải lên</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END timeline item -->
            <?
            } // End while
            ?>

            <div>
                <i class="fa fa-check bg-blue"></i>
            </div>
            <div style="text-align: center">
                <a class="btn btn-primary" href="update_status.php?type=update&record_id=<?= $record_id ?>"><i class="fa fa-check" aria-hidden="true"></i> Duyệt ảnh</a>
                <a class="btn btn-danger" href="<?= base64_decode($fs_redirect) ?>"><i class="fa fa-times" aria-hidden="true"></i> Không duyệt</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>