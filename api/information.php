<?php
	$dirPWroot = str_repeat("../", substr_count($_SERVER['PHP_SELF'], "/")-1);
	require_once($dirPWroot."resource/php/extend/_RGI.php");
	// Execute
	$self = $_SESSION["auth"]["user"]; $year = $_SESSION["stif"]["t_year"]; $grade = $_SESSION["auth"]["info"]["grade"]; $room = $_SESSION["auth"]["info"]["room"];
	$cond = "year=$year AND grade=$grade AND room=$room";
	if (empty($self)) errorMessage(3, "You are not signed-in. Please reload and try again."); else
	switch ($type) {
		case "group": {
			switch ($command) {
				case "title": {
					# $code = escapeSQL($attr["code"]);
					$getcode = $db -> query("SELECT code FROM PBL_group WHERE year=$year AND $self IN(mbr1,mbr2,mbr3,mbr4,mbr5,mbr6,mbr7)");
					if (!$getcode) errorMessage(3, "Error loading your data. Please try again.");
					else if (!$getcode -> num_rows) {
						successState(array("isGrouped" => false));
						slog("PBL", "load", "info", $code, "fail", "", "NotExisted");
					} else {
						$code = ($getcode -> fetch_array(MYSQLI_ASSOC))["code"];
						// No COALESCE on score -> only show when all is graded
						$get = $db -> query("SELECT a.nameth,a.nameen,a.type,a.adv1,a.adv2,a.adv3,a.score_poster+c.score+(CASE WHEN ROUND(SUM(b.total)/COUNT(b.cmte))<50 THEN 2 ELSE 3 END) AS score FROM PBL_group a LEFT JOIN PBL_score b ON b.code=a.code LEFT JOIN user_score c ON c.stdid=$self AND c.year=a.year AND c.subj='PBL' AND c.field='oph-act' WHERE a.code='$code' GROUP BY b.code");
						if (!$get) {
							errorMessage(3, "Error loading your data. Please try again.");
							slog("PBL", "load", "info", $code, "fail", "", "InvalidQuery");
						} else if (!$get -> num_rows) {
							errorMessage(3, "There's an error: Your group information is unavailable. Please try reloading.");
							slog("PBL", "load", "info", $code, "fail", "", "NotExisted");
						} else {
							$read = $get -> fetch_array(MYSQLI_ASSOC);
							successState($read);
						}
					}
				} break;
				case "member": {
					# $code = escapeSQL($attr["code"]);
					$getcode = $db -> query("SELECT code FROM PBL_group WHERE year=$year AND $self IN(mbr1,mbr2,mbr3,mbr4,mbr5,mbr6,mbr7)");
					if (!$getcode) errorMessage(3, "Error loading your data. Please try again.");
					else if (!$getcode -> num_rows) {
						successState(array("isGrouped" => false));
						slog("PBL", "load", "member", $code, "fail", "", "NotExisted");
					} else {
						$code = ($getcode -> fetch_array(MYSQLI_ASSOC))["code"];
						$get = $db -> query("SELECT mbr1,mbr2,mbr3,mbr4,mbr5,mbr6,mbr7,statusOpen,publishWork FROM PBL_group WHERE code='$code'");
						if (!$get) {
							errorMessage(3, "Error loading your data. Please try again.");
							slog("PBL", "load", "member", $code, "fail", "", "InvalidQuery");
						} else if (!$get -> num_rows) {
							errorMessage(3, "There's an error: Your group information is unavailable. Please try reloading.");
							errorMessage(1, "Code: $code");
							slog("PBL", "load", "member", $code, "fail", "", "NotExisted");
						} else {
							$read = $get -> fetch_array(MYSQLI_ASSOC);
							$data = array("settings" => array(
								"statusOpen" => $read["statusOpen"],
								"publishWork" => $read["publishWork"]
							));
							for ($_ = 0; $_ < count($data["settings"]); $_++) array_pop($read);
							$data["list"] = array_values(array_filter($read));
							successState($data);
						}
					}
				} break;
				default: errorMessage(1, "Invalid command"); break;
			}
		} break;
		case "person": {
			switch ($command) {
				case "student": {
					$query = escapeSQL($attr);
					if (!preg_match("/^\d{5}(,\d{5})*$/", $query)) {
						errorMessage(3, "Error: Invalid student's referer.");
						slog("PBL", "load", "info", $query, "fail", "", "InvalidValue");
					} else {
						$first = explode(",", $query)[0];
						$get = $db -> query("SELECT stdid,number,namep,namefth,namelth,namenth FROM user_s WHERE stdid IN($query) ORDER BY (CASE stdid WHEN $first THEN 1 ELSE 2 END),number");
						if (!$get) {
							errorMessage(3, "Error loading your data. Please try again.");
							slog("PBL", "load", "info", $query, "fail", "", "InvalidQuery");
						} else if (!$get -> num_rows) {
							errorMessage(3, "There's an error: Your group information is unavailable. Please try reloading.");
							slog("PBL", "load", "info", $query, "fail", "", "NotExisted");
						} else {
							$data = array("list" => array());
							while ($read = $get -> fetch_assoc()) array_push($data["list"], array(
								"ID" => $read["stdid"],
								"fullname" => prefixcode2text($read["namep"])["th"].$read["namefth"]."  ".$read["namelth"],
								"nickname" => $read["namenth"],
								"number" => $read["number"]
							)); successState($data);
						}
					}
				} break;
				case "teacher": {
					$query = escapeSQL($attr);
					if (!preg_match("/^([a-z]{3,28}\.[a-z]{1,2}|[a-zA-Z]{3,30}\d{0,3})(,([a-z]{3,28}\.[a-z]{1,2}|[a-zA-Z]{3,30}\d{0,3}))*$/", $query)) {
						errorMessage(3, "Error: Invalid teacher's referer.");
						slog("PBL", "load", "info", $query, "fail", "", "InvalidValue");
					} else {
						$search = "'".implode("','", explode(",", $query))."'";
						$get = $db -> query("SELECT namecode,namefth,namelth FROM user_t WHERE namecode IN($search)");
						if (!$get) {
							errorMessage(3, "Error loading your data. Please try again.");
							slog("PBL", "load", "info", $query, "fail", "", "InvalidQuery");
						} else if (!$get -> num_rows) {
							errorMessage(3, "There's an error: Your group information is unavailable. Please try reloading.");
							slog("PBL", "load", "info", $query, "fail", "", "NotExisted");
						} else {
							$data = array("list" => array());
							while ($read = $get -> fetch_assoc())
								$data["list"][$read["namecode"]] = "ครู".$read["namefth"]."  ".$read["namelth"];
							successState($data);
						}
					}
				} break;
				default: errorMessage(1, "Invalid command"); break;
			}
		} break;
		case "settings": {
			switch ($command) {
				case "member": {
					$setName = escapeSQL($attr[0]); $newValue = escapeSQL($attr[1]);
					# $code = escapeSQL($attr["code"]);
					$getcode = $db -> query("SELECT code,mbr1 FROM PBL_group WHERE year=$year AND $self IN(mbr1,mbr2,mbr3,mbr4,mbr5,mbr6,mbr7)");
					if (!$getcode) errorMessage(3, "Error loading your data. Please try again.");
					else if (!$getcode -> num_rows) {
						successState(array("isGrouped" => false));
						slog("PBL", "edit", "setting", "$code: $setName -> $newValue", "fail", "", "NotExisted");
					} else {
						$readcode = $getcode -> fetch_array(MYSQLI_ASSOC);
						$code = $readcode["code"];
						if ($self <> $readcode["mbr1"]) {
							errorMessage(3, "Only group leader can change settings, which you are not.");
							slog("PBL", "edit", "setting", "$code: $setName -> $newValue", "fail", "", "NotEligible");
						} else if (!in_array($setName, array("statusOpen", "publishWork"))) {
							errorMessage(3, "The settings you are trying to update doesn't exist.");
							slog("PBL", "edit", "setting", "$code: $setName -> $newValue", "fail", "", "Unavailable");
						} else {
							$success = $db -> query("UPDATE PBL_group SET $setName='$newValue' WHERE code='$code'");
							if ($success) {
								successState(array("message" => array(
									array(0, "Setting changes saved.")
								))); slog("PBL", "edit", "setting", "$code: $setName -> $newValue", "pass");
							} else {
								errorMessage(3, "Unable to save your setting ($setName).");
								slog("PBL", "edit", "setting", "$code: $setName -> $newValue", "fail", "", "InvalidQuery");
							}
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