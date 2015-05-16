<?php

	function getPlayerUpgradeLevel($player_id, $upgrade) {
		
		// This function returns details of the galaxy the supplied system is in
	
		addToDebugLog("getPlayerUpgradeLevel(): Function Entry - supplied parameters: Player ID: " . $player_id . ", Upgrade: " . $upgrade);
	
		$sql = "SELECT * FROM startrade.upgrades WHERE player_id = " . $player_id . " AND upgrade_name = '" . $upgrade . "';";
		addToDebugLog("getPlayerUpgradeLevel(): Constructed SQL: " . $sql);
		$upgrade_details = search($sql);
	
		// 0	upgrade_id
		// 1	player_id
		// 2	upgrade_name
		// 3	upgrade_level
	
		return $upgrade_details;
	
	}

?>