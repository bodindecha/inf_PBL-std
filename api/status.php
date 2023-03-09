<?php
	$dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require_once($dirPWroot."resource/php/extend/_RGI.php"); # require_once($dirPWroot."resource/php/core/reload_settings.php");
	// Execute
	$self = $_SESSION["auth"]["user"]; $year = $_SESSION["stif"]["t_year"]; $grade = $_SESSION["auth"]["info"]["grade"]; $room = $_SESSION["auth"]["info"]["room"];
	$cond = "year=$year AND grade=$grade AND room=$room";
	if (empty($self)) errorMessage(3, "You are not signed-in. Please reload and try again."); else
	switch ($type) {
		case "get": {
			switch ($command) {
				case "personal": {
					$get = $db -> query("SELECT code FROM PBL_group WHERE year=$year AND $self IN(mbr1,mbr2,mbr3,mbr4,mbr5,mbr6,mbr7)");
					if (!$get) errorMessage(3, "Error loading your data.");
					else {
						$data = array("isGrouped" => boolval($get -> num_rows));
						if ($data["isGrouped"]) {
							$data["code"] = ($get -> fetch_array(MYSQLI_ASSOC))["code"];
							$data["requireIS"] = ($grade == 2 || $grade == 4);
						} successState($data);
					}
				} break;
				case "fileLink": {
					$fileCfg = array(
						"mindmap"		=> "Mindmap",
						"IS1-1"			=> "ใบงาน IS1-1",
						"IS1-2"			=> "ใบงาน IS1-2",
						"IS1-3"			=> "ใบงาน IS1-3",
						"report-1"		=> "รายงานโครงงานบทที่ 1",
						"report-2"		=> "รายงานโครงงานบทที่ 2",
						"report-3"		=> "รายงานโครงงานบทที่ 3",
						"report-4"		=> "รายงานโครงงานบทที่ 4",
						"report-5"		=> "รายงานโครงงานบทที่ 5",
						"report-all"	=> "รายงานฉบับสมบูรณ์",
						"abstract"		=> "Abstract",
						"poster"		=> "Poster"
					); $fileExts = array("png", "jpg", "jpeg", "heic", "heif", "gif", "pdf");
					// Fetch data
					$filePos = array_search($attr, array_keys($fileCfg));
					$get = $db -> query("SELECT code,year,grade,fileStatus,fileType FROM PBL_group WHERE year=$year AND $self IN(mbr1,mbr2,mbr3,mbr4,mbr5,mbr6,mbr7)");
					if (!$get) errorMessage(3, "Error loading your data. Please try again.");
					else if (!$get -> num_rows)
						successState(array("isGrouped" => false));
					else {
						$read = $get -> fetch_array(MYSQLI_ASSOC);
						$code = $read["code"];
						if (intval($read["fileStatus"])&pow(2, $filePos)) {
							$extension = explode(";", $read["fileType"])[$filePos];
							$year = $read["year"]; $grade = $read["grade"];
							$path = "upload/PBL/$year/$attr/$grade/$code.$extension";
							$finder = $dirPWroot."resource/$path";
							if (file_exists($finder)) successState(array(
								"preview" => "/resource/file/viewer?furl=".urlencode($path)."&name=$code%20-%20$fileCfg[$attr]",
								"download" => "/resource/file/download?furl=".urlencode($path)."&name=$code%20-%20$fileCfg[$attr]",
								"print" => base64_encode("/resource/$path")
							)); else errorMessage(3, "File not found");
						} else errorMessage(3, "File has not been submitted");
					}
				} break;
				default: errorMessage(1, "Invalid command"); break;
			}
		} break;
		case "work": {
			switch ($command) {
				case "deadline": {
					$get = $db -> query("SELECT name,value FROM config_sep WHERE name LIKE 'PBL-dd_%' ORDER BY name");
					if (!$get) errorMessage(3, "Error loading your data.");
					else {
						function date2thai($date) {
							$dt = explode("-", $date);
							return "วันที่ ".$dt[2]." ".month2text($dt[1])["th"][1]." ".strval(intval($dt[0])+543);
						}
						$data = array(); while ($read = $get -> fetch_assoc()) {
							$readable = (empty($read["value"]) ? "ยังไม่กำหนดวัน" : date2thai($read["value"]));
							$comparable = (empty($read["value"]) ? date("Y-m-d H:i:s", strtotime("+1 year")) : $read["value"]." 23:59:59");
							$data[substr($read["name"], 7)] = array($readable, $comparable);
						} successState($data);
					}
				} break;
				case "file": {
					$fileCfg = array("mindmap", "IS1-1", "IS1-2", "IS1-3", "report-1", "report-2", "report-3", "report-4", "report-5", "report-all", "abstract", "poster");
					# $code = escapeSQL($attr["code"]);
					$getcode = $db -> query("SELECT code,CONCAT(nameth,nameen) AS name,type,CONCAT(COALESCE(adv1,''),COALESCE(adv2,''),COALESCE(adv3,'')) AS adv,fileStatus FROM PBL_group WHERE year=$year AND $self IN(mbr1,mbr2,mbr3,mbr4,mbr5,mbr6,mbr7)");
					if (!$getcode) errorMessage(3, "Error loading your data. Please try again.");
					else if (!$getcode -> num_rows) {
						successState(array("isGrouped" => false));
						slog("PBL", "edit", "info", $code, "fail", "", "NotExisted");
					} else {
						$readcode = $getcode -> fetch_array(MYSQLI_ASSOC);
						$code = $readcode["code"];
						$status = intval($readcode["fileStatus"]);
						if (!empty($attr)) {
							$pos = array_search($attr, $fileCfg);
							successState(array(
								"fileSent" => boolval($status&pow(2, $pos))
							));
						} else {
							$status = strrev(substr(base_convert(pow(2, count($fileCfg))|$status, 10, 2), 1));
							function bit2bool($fileStatus) {
								return boolval($fileStatus);
							} $status = array_combine($fileCfg, array_map("bit2bool", str_split($status)));
							$status["n1"] = boolval(strlen($readcode["name"]));
							$status["n2"] = boolval(strlen($readcode["adv"]));
							$status["n3"] = boolval(strlen($readcode["type"]));
							successState($status);
						}
					}
				} break;
				default: errorMessage(1, "Invalid command"); break;
			}
		} break;
		default: errorMessage(1, "Invalid type"); break;
	} $db -> close();
	sendOutput($return);
?>