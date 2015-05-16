<html>

<head>
	<title>Upgrades</title>
</head>

<body>

<h2>Player Upgrades</h2>

<?php

	include 'functions/debug_functions.php';
	include 'functions/mysql_functions.php';
	//include 'functions/startrade_functions.php';
	//include 'functions/galaxy_functions.php';
	//include 'functions/marketplace_functions.php';
	//include 'functions/transaction_functions.php';
	include 'functions/upgrade_functions.php';
	
	global $current_player;
	$current_player = $_COOKIE['Startrade:CurrentUser'];

	echo "<table border=1 cellspacing=0 cellpadding=3>";
	echo "<tr><td>Upgrade<td>Description<td align=center>Level<td>Next Level</tr>";
	
	// Best Planet
	// 0	No Help at all
	// 1	Either System Name or Planet Name
	// 2	Both Names available
	
	echo "<tr><td>Best Planet<td>In Cargohold, shows the planet with the best known price";
	$upgrade_details = getPlayerUpgradeLevel($current_player, 'best_planet');
	$player_level = $upgrade_details[0][3];
	echo "<td align=center>" . $player_level;
	if ($player_level < 2) {
		$next_level = $player_level+1;
		echo "<td><a href='buyUpgrade.php?player_id=" . $current_player . "&upgrade_id=1&new_level=" . $next_level . "'>Buy Level " . $next_level . "</a>";
	} else {
		echo "<td>Max Level";
	}
	echo "</tr>";
	
	// Transaction History
	// 0	No transactions
	// 1	Basic history, 5 transactions
	// 2	Basic history, 10 transactions
	// 3	Advanced history, 5 transactions
	// 4	Advanced history, 10 transactions
	
	echo "<tr><td>Transaction History<td>Determines how much information about previous transactions is displayed";
	$upgrade_details = getPlayerUpgradeLevel($current_player, 'txn_history');
	$player_level = $upgrade_details[0][3];
	echo "<td align=center>" . $player_level;
	if ($player_level < 4) {
		$next_level = $player_level+1;
		echo "<td align=center><a href='buyUpgrade.php?player_id=" . $current_player . "&upgrade_id=2&new_level=$next_level'>Buy Level " . $next_level . "</a>";
	} else {
		echo "<td>Max Level";
	}
	echo "</tr>";
	
	
	

	echo "</table>";

	outputDebugLog();
	
?>

</body>

</html>