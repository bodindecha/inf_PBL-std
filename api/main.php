<?php
    $dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
    require_once($dirPWroot."resource/php/extend/_RGI.php");
    // Execute
	$self = $_SESSION["auth"]["user"]; $year = $_SESSION["stif"]["t_year"]; $grade = $_SESSION["auth"]["info"]["grade"]; $room = $_SESSION["auth"]["info"]["room"];
	$cond = "year=$year AND grade=$grade AND room=$room";
	if (empty($self)) errorMessage(3, "You are not signed-in. Please reload and try again."); else
    switch ($type) {
        case "work": {
			switch ($command) {
				case "remove": {
					$fileCfg = array("mindmap", "IS1-1", "IS1-2", "IS1-3", "report-1", "report-2", "report-3", "report-4", "report-5", "report-all", "abstract", "poster");
					$filePos = array_search($attr, array_keys($fileCfg));
					# $code = escapeSQL($attr["code"]);
					$get = $db -> query("SELECT code,year,grade,fileStatus,fileType FROM PBL_group WHERE year=$year AND $self IN(mbr1,mbr2,mbr3,mbr4,mbr5,mbr6,mbr7)");
					if (!$get) errorMessage(3, "Error loading your data. Please try again.");
					else if (!$get -> num_rows) {
						successState(array("isGrouped" => false));
						slog("PBL", "del", "file", "$code: $attr", "fail", "", "NotExisted");
					} else {
						$read = $get -> fetch_array(MYSQLI_ASSOC);
						$code = $read["code"];
						// If memory exists
						if (intval($read["fileStatus"])&pow(2, $filePos)) {
							$extension = explode(";", $read["fileType"])[$filePos];
							$year = $read["year"]; $grade = $read["grade"];
							$path = "resource/upload/PBL/$year/$attr/$grade/$code.$extension";
							$finder = $dirPWroot.$path;
							if (file_exists($finder)) {
								if (unlink($finder)) {
									$fileTypes = explode(";", $read["fileType"]);
									$fileTypes[$filePos] = ""; $fileTypes = implode(";", $fileTypes);
									$success = $db -> query("UPDATE PBL_group SET fileStatus=~(~fileStatus|".pow(2, $filePos)."),fileType='$fileTypes' WHERE code='$code'");
									if ($success) {
										successState();
										slog("PBL", "del", "file", "$code: $attr", "pass");
									} else {
										errorMessage(3, "Unable to remove file.");
										slog("PBL", "del", "file", "$code: $attr", "fail", "", "InvalidQuery");
									}
								} else {
									errorMessage(3, "Unable to delete file.");
									slog("PBL", "del", "file", "$code: $attr", "fail", "", "Incorrect");
								}
							} else {
								errorMessage(3, "No file to remove.");
								slog("PBL", "del", "file", "$code: $attr", "fail", "", "Empty");
							}
						} else {
							errorMessage(3, "File not found.");
							slog("PBL", "del", "file", "$code: $attr", "fail", "", "Unavailable");
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