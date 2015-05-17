<?php 	setcookie("Startrade:CurrentUser", $_GET['player_id']); ?>

<html>

<head>
	<title>Current Planet</title>
</head>

<body>

<?php

	include 'functions/debug_functions.php';
	include 'functions/mysql_functions.php';
	include 'functions/startrade_functions.php';
	include 'functions/galaxy_functions.php';
	include 'functions/marketplace_functions.php';
	include 'functions/transaction_functions.php';
	include 'functions/upgrade_functions.php';
	
	global $current_player;
	$current_player = $_GET['player_id'];

		// [0]	Planet ID
		// [1]	Planet Name
		// [2]	System ID
		// [3]	Planet Type
	
	// Get players current planet
	$current_planet_details = currentPlanetDetails($_GET['player_id']);
	$current_planet_details = explode(",", $current_planet_details);
	$current_planet_type = $current_planet_details[3];
	$current_planet_name = $current_planet_details[1];
	
	$galaxy_details = galaxyDetails($current_planet_details[2]);
	
	echo "<h2>Welcome to [" . $galaxy_details[0][0] . ":" . $current_planet_details[2] . ":" . $current_planet_details[0] . "." . substr($current_planet_type,0,1) . "] " . $current_planet_details[1] . ", " . $galaxy_details[0][1] . " galaxy</h2>";
	
	// Write commodities
	echo "<table>";
	echo "<tr height=50 bgcolor=#ccc><td valign=center align=center>";
	echo "<a href='depart.php?player_id=" . $_GET['player_id'] . "'>Depart</a>";
	echo "<td align=center valign=center>";
	if ($current_planet_type == "Industrial") { // Industrial Planet
		echo "<a href='shipyard.php?player_id=" . $_GET['player_id'] . "&planet_id=" . $_GET['planet_id'] . "'>Visit Shipyard</a>";
	}
	if ($current_planet_type == "Advanced") { // Advanced Planet
		echo "<a href='upgrades.php?player_id=" . $_GET['player_id'] . "&planet_id=" . $_GET['planet_id'] . "'>Buy Upgrades</a>";
	}
	echo "</tr>";
	echo "<tr><td valign=top>";
		marketPlace($_GET['planet_id']);

	echo "<td valign=top>";
		cargo($_GET['player_id']);
		displayTransactions($_GET['player_id']);
	echo "</tr></table>";

	//outputDebugLog();
	
?>

</body>

</html>