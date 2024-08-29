<?php
	$APP_RootDir = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/"));
	require($APP_RootDir."private/script/start/PHP.php");
	$header["title"] = "IS & PBL - Student";
	$header["desc"] = "ระบบจัดการโครงงาน IS และ PBL นักเรียน";

	if (isset($_REQUEST["gjc"])) header("Location: /v2/s/PBL/#/group/join/".$_REQUEST["gjc"]);
	else header("Location: /v2/s/PBL/");
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php require($APP_RootDir."private/block/core/heading.php"); require($APP_RootDir."private/script/start/CSS-JS.php"); ?>
		<style type="text/css">
			
		</style>
		<script type="text/javascript">
			// const TRANSLATION = location.pathname.substring(1).replace(/\/$/, "").replaceAll("/", "+");
			$(document).ready(function() {
				page.init();
			});
			const page = (function(d) {
				const cv = { API_URL: AppConfig.APIbase + "" };
				var sv = {inited: false};
				var initialize = function() {
					if (sv.inited) return;

					sv.inited = true;
				};
				var myFunction = function() {

				};
				return {
					init: initialize,
					myFunction
				};
			}(document));
		</script>
	</head>
	<body>
		<app name="main">
			<?php require($APP_RootDir."private/block/core/top-panel/structure.php"); ?>
			<main>
				<section class="container">
					<h2><?=$header["title"]?></h2>

				</section>
			</main>
			<?php require($APP_RootDir."private/block/core/material/main.php"); ?>
		</app>
	</body>
</html>