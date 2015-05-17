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

	function buyUpgrade($player_id, $upgrade_name, $new_level, $price) {
		
		// This function purchases upgrades
	
		addToDebugLog("buyUpgrade(): Function Entry - supplied parameters: Player ID: " . $player_id . ", Upgrade ID: " . $upgrade_id . ", New Level: " . $new_level . ", Price: " . $price);		
		
		// Upgrade players upgrade level
			$dml = "UPDATE startrade.upgrades SET upgrade_level = " . $new_level . " WHERE player_id = " . $player_id . " AND upgrade_name = '" . $upgrade_name . "';";
			addToDebugLog("buyUpgrade(): DML generated: "  .$dml);
			$result = insert($dml);
			if ($result == TRUE) {
				addToDebugLog("buyUpgrade(): Upgrade purchased");
			} else {
				addToDebugLog("buyUpgrade(): ERROR: Upgrade not purchased");
			}
		
		// Remove funds from player's funds
		$dml = "UPDATE startrade.player SET current_funds = current_funds - " . $price . " WHERE player_id = " . $player_id . ";";
		addToDebugLog("buyUpgrade(): DML generated: "  .$dml);
		$result = insert($dml);
		if ($result == TRUE) {
			addToDebugLog("buyUpgrade(): Player funds updated");
		} else {
			addToDebugLog("buyUpgrade(): ERROR: Player funds not updated");
		}
	
	}

?>