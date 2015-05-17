<html>

<head>
	<title>Upgrades</title>
</head>

<body>

<h2>Player Upgrades</h2>

<?php

	include 'functions/debug_functions.php';
	include 'functions/mysql_functions.php';
	include 'functions/startrade_functions.php';
	//include 'functions/galaxy_functions.php';
	//include 'functions/marketplace_functions.php';
	//include 'functions/transaction_functions.php';
	include 'functions/upgrade_functions.php';
	
	global $current_player;
	$current_player = $_COOKIE['Startrade:CurrentUser'];
	if ($current_player == "") {
		$current_player = $_GET['player_id'];
	}

	// Need to determine if the player can afford the next level
	$funds = playerCurrentCredits($current_player);
	
	echo "<table border=1 cellspacing=0 cellpadding=3>";
	echo "<tr bgcolor=#82caff><td>Upgrade<td>Description<td>Current Level<td>Next Level</tr>";
	
	// *************************************************************************************************************************************************************************
	// Best Planet
	// *************************************************************************************************************************************************************************

	$best_planet_details = array(
			array(0, "No Help at all", 0),
			array(1, "Either System Name or Planet Name", 1000000),
			array(2, "Both Names available", 5000000),
	);
	
	echo "<tr><td>Best Planet<td>In Cargohold, shows the planet with the best known price";
	$upgrade_details = getPlayerUpgradeLevel($current_player, 'best_planet');
	$player_level = $upgrade_details[0][3];
	echo "<td>" . $player_level . " (" . $best_planet_details[$player_level][1] . ")";
	if ($player_level < 2) {
		$next_level = $player_level+1;
		if ($funds > $best_planet_details[$next_level][2]) {
			echo "<td><a href='buyUpgrade.php?player_id=" . $current_player . "&upgrade_name=best_planet&new_level=" . $next_level . "&price=" . $best_planet_details[$next_level][2] . "' target='_blank'>Buy Level " . $next_level . " for " . $best_planet_details[$next_level][2] . "Cr";
		} else {
			$shortfall = $best_planet_details[$next_level][2] - $funds;
			echo "<td>" . $shortfall . "Cr more needed";				
		}
		
		echo " (" . $best_planet_details[$next_level][1] . ")</a>";
	} else {
		echo "<td>Max Level";
	}
	echo "</tr>";
	
	// *************************************************************************************************************************************************************************
	// Transaction History
	// *************************************************************************************************************************************************************************

	$txn_history_details = array(
			array(0, "No transactions", 0),
			array(1, "Basic history, 5 transactions", 10000),
			array(2, "Basic history, 10 transactions", 100000),
			array(3, "Advanced history, 5 transactions", 500000),
			array(4, "Advanced history, 10 transactions", 1000000),
	);	
	
	echo "<tr><td>Transaction History<td>Determines how much information about previous transactions is displayed";
	$upgrade_details = getPlayerUpgradeLevel($current_player, 'txn_history');
	$player_level = $upgrade_details[0][3];
	echo "<td>" . $player_level . " (" . $txn_history_details[$player_level][1] . ")";
	if ($player_level < 4) {
		$next_level = $player_level+1;
		if ($funds > $fleet_size_details[$next_level][2]) {
			echo "<td align=center><a href='buyUpgrade.php?player_id=" . $current_player . "&upgrade_name=txn_history&new_level=" . $next_level . "&price=" . $txn_history_details[$next_level][2] . "' target='_blank'>Buy Level " . $next_level . " for " . $txn_history_details[$next_level][2] . "Cr";
		} else {
			$shortfall = $txn_history_details[$next_level][2] - $funds;
			echo "<td>" . $shortfall . "Cr more needed";			
		}
		echo " (" . $txn_history_details[$next_level][1] . ")</a>";
	} else {
		echo "<td>Max Level";
	}
	echo "</tr>";
	
	// *************************************************************************************************************************************************************************
	// Fleet Size
	// *************************************************************************************************************************************************************************
	
	$fleet_size_details = array(
			array(0, "Single Ship", 0),
			array(1, "Three Ships", 1000000),
			array(2, "Six Ships", 3000000),
			array(3, "Ten Ships", 5000000),
			array(4, "Unlimited Ships", 10000000),
	);	
	
	echo "<tr><td>Fleet Size<td>Determines how many ships you can have in your fleet";
	$upgrade_details = getPlayerUpgradeLevel($current_player, 'fleet_size');
	$player_level = $upgrade_details[0][3];
	echo "<td>" . $player_level . " (" . $fleet_size_details[$player_level][1] . ")";

	if ($player_level < 4) {
		$next_level = $player_level+1;
		if ($funds > $fleet_size_details[$next_level][2]) {
			echo "<td><a href='buyUpgrade.php?player_id=" . $current_player . "&upgrade_name=fleet_size&new_level=" . $next_level . "&price=" . $fleet_size_details[$next_level][2] . "' target='_blank'>Buy Level " . $next_level . " for " . $fleet_size_details[$next_level][2] . "Cr";
		} else {
			$shortfall = $fleet_size_details[$next_level][2] - $funds;
			echo "<td>" . $shortfall . "Cr more needed";
		}
		echo " (" . $fleet_size_details[$next_level][1] . ")</a>";
	} else {
		echo "<td>Max Level";
	}
	echo "</tr>";	
	
	
	
	echo "</table>";
	echo "<p><a href='planet.php?planet_id=" . $_GET['planet_id'] . "&player_id=" . $_GET['player_id'] . "'>Back to Marketplace</a>";

	//outputDebugLog();
	
?>

</body>

</html>