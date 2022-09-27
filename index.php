<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require($dirPWroot."resource/hpe/init_ps.php");
	$header_title = "IS & PBL - Student";
	$header_desc = "ระบบบริหารโครงงาน PBL";
	$home_menu = "is-pbl";
    $forceExternalBrowser = true;
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($dirPWroot."resource/hpe/heading.php"); require($dirPWroot."resource/hpe/init_ss.php"); ?>
		<style type="text/css">
            main {
                --sb-size: 50px;
            }
            main div.container { overflow: visible !important; }
            main p { margin: 0 0 10px; }
			main .icon, main i.--material-icons, main i.fa[class~="fa-"] { display: flex; align-items: center; }
            main .title-btn { display: flex; justify-content: space-between; align-items: center; }
            main h2:first-child a {
                color: var(--clr-bs-gray) !important;
                transition: var(--time-tst-xfast);
            }
            main h2:first-child a:hover { color: var(--clr-bs-gray-dark) !important; text-decoration: none !important; }
            main div.container .wrapper {
                box-shadow: 0 0 var(--shd-big) var(--fade-black-7);
                border-radius: 10px;
            }
            main p { margin: 0 0 10px; }
            /* Notify-js customizer */
            main .notifyjs-PBL-unsaved-base {
                width: 300px;
                background-color: #FCF8E3;
                border: 1px solid #FBEED5; border-radius: 5px;
            }
            main .notifyjs-PBL-unsaved-base .title {
                padding: 5px;
                text-shadow: 0 1px 0 rgb(255 255 255 / 50%);
                text-align: center; white-space: nowrap;
            }
            main .notifyjs-PBL-unsaved-base .form {
                padding: 2.5px 5px 5px;
                justify-content: center; flex-wrap: nowrap !important;
            }
            main .notifyjs-PBL-unsaved-base button {
                padding: 0 5px;
                font-size: medium; white-space: nowrap;
            }
            @media only screen and (max-width: 768px) {
                main .notifyjs-PBL-unsaved-base button { font-size: small; }
            }
		</style>
        <link rel="stylesheet" href="/s/PBL/v2/components.min.css" />
		<link rel="stylesheet" href="/resource/css/extend/all-PBL.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
		<script type="text/javascript">
			$(document).ready(function() {
                PBL.init();
            });
            top.USER = "<?=$_SESSION['auth']['user']?>";
            const pUI = {
                select: {
                    advisor: function(no) {
                        let ai = no.toString(), exc = "", exclude = [
                            document.querySelector('input[name="adv1"]').value,
                            document.querySelector('input[name="adv2"]').value,
                            document.querySelector('input[name="adv3"]').value
                        ]; if (exclude[0] != "") exc += exclude [0]; 
                        if (exclude[1] != "") exc += (exc == "" ? "" : ",") + exclude[1];
                        if (exclude[2] != "") exc += (exc == "" ? "" : ",") + exclude[2];
                        fst.start("เลือกครูที่ปรึกษาโครงงานคนที่ "+ai, 'input[name="adv'+ai+'"]', 'input[name="adv'+ai+'"] + input[readonly]', exc);

                    }
                }, show: {
                    ungrouped: function() {
                        app.ui.lightbox.open("top", {title: "เพื่อนที่ยังไม่มีกลุ่ม", allowclose: true, html: '<iframe src="/resource/php/std-list?mode=PBL_no-group&grade=<?=$_SESSION['auth']['info']['grade']?>&room=<?=$_SESSION['auth']['info']['room']?>" style="width:90vw;height:80vh;border:none">Loading...</iframe>'});
                    }, code: function() {
                        var button = $('main .page[path="group/members"] .action button:nth-child(1)');
                        if ($('main .page[path="group/members"] .code .expand').toggleClass("emphasize").is(".emphasize")) {
                            button.children().first().text("fullscreen_exit");
                            button.attr("data-title", "ย่อโค้ด");
                        } else {
                            button.children().first().text("fullscreen");
                            button.attr("data-title", "ขยายโค้ด");
                        }
                    }, QRcode: function() {
                        var url = location.hostname+location.pathname+"#/group/join/"+PBL.groupCode();
                        app.ui.lightbox.open("top", {allowclose: true, autoclose: 60000, title: "QRcode เข้าร่วมกลุ่ม IS/PBL", html: '<img width="325" height="325" src="/resource/images/QRcode?url='+encodeURIComponent(url)+'" draggable="false" /><center>'+PBL.groupCode()+'</center>'});
                    }
                }, form: {
                    validate: function() {
                        $('main .page[path="group/members"] .settings > *:focus-within button').removeAttr("disabled");
                        PBL.setState("loadSettingsOver", false); PBL.confirmLeave(PBL.pageURL());
                    },
                    btnState: function() {
                        PBL.btnAction.unfreeze();
                    }
                }, copy: function(type) {
                    switch (type) {
                        case "code": app.io.copy.content(PBL.groupCode()); break;
                        case "link": app.io.copy.content("https://"+location.hostname+location.pathname+"#/group/join/"+PBL.groupCode()); break;
                    }
                }
            };
            const validate_field = pUI.form.btnState;
		</script>
		<script type="text/javascript" src="/s/PBL/v2/PBL-student.min.js"></script>
		<script type="text/javascript" src="/resource/js/extend/all-PBL.js"></script>
		<script type="text/javascript" src="/resource/js/extend/fs-teacher.js"></script>
		<script type="text/javascript" src="/resource/js/lib/print.min.js"></script>
		<script type="text/javascript" src="/resource/js/lib/notify.min.js"></script>
	</head>
	<body>
		<?php require($dirPWroot."resource/hpe/header.php"); ?>
		<main shrink="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
			<div class="container">
				<h2 class="title-btn"><?=$header_desc?> <a href="javascript:PBL.help();" class="icon"><i class="fa fa-question-circle"></i></a></h2>
				
			</div>
            <div class="manual" hidden>
                <style type="text/css">
                    .lightbox .manual {
                        margin: 12.5px 10px 10px;
                        display: flex; justify-content: center; flex-direction: column; gap: 10px;
                        transition: var(--time-tst-fast) cubic-bezier(0.34, 1.56, 0.64, 1);
                    }
                    .lightbox .manual a[role="button"] {
                        /* min-width: 50px; max-height: 35px; */
                        transition: var(--time-tst-fast) cubic-bezier(0.34, 1.56, 0.64, 1);
                    }
                    /* .lightbox .manual a[role="button"] > *:first-child {
                        margin-right: 10px;
                        width: 35px; height: 35px; line-height: 35px;
                        box-shadow: 1.25px 1.25px var(--shd-tiny) var(--clr-main-black-absolute);
                        border-radius: 7.5px;
                        object-fit: contain;
                    } */
                    @media only screen and (max-width: 768px) {
                        /* .lightbox .manual {
                            margin: 10px;
                            flex-direction: row; flex-wrap: wrap;
                        } */
                    }
                </style>
                <div class="manual">
                    <a role="button" class="blue" href="javascript:PBL.help('document')" data-href="/go?url=">
                        <i class="fa fa-file-text-o"></i>
                        <span>คู่มือการใช้งานระบบ</span>
                    </a>
                    <a role="button" class="red" href="javascript:PBL.help('mediaVDO')" data-href="/go?url=">
                        <i class="fa fa-youtube-play"></i>
                        <span>วิดีโอแนะนำการใช้งาน</span>
                    </a>
                </div>
            </div>
		</main>
		<?php require($dirPWroot."resource/hpe/material.php"); ?>
		<footer>
			<?php require($dirPWroot."resource/hpe/footer.php"); ?>
		</footer>
	</body>
</html>