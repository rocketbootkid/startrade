<html>

<head>
	<title>Shipyard</title>
</head>

<body>

<?php

	include 'functions/debug_functions.php';
	include 'functions/startrade_functions.php';
	include 'functions/mysql_functions.php';
	include 'functions/shipyard_functions.php';
	include 'functions/upgrade_functions.php';
	
	// Get player's current planet
	$current_planet_details = currentPlanetDetails($_GET['player_id']);
	$current_planet_details = explode(",", $current_planet_details);
	$current_planet_type = $current_planet_details[3];
	$current_planet_name = $current_planet_details[1];

	echo "<h2>Welcome to [" . $current_planet_details[2] . ":" . $current_planet_details[0] . "] " . $current_planet_details[1] . " Shipyard</h2>";
	
	if ($current_planet_type == "Industrial") { // Industrial Planet
	
		echo "<table>";
		echo "<tr><td height=50 bgcolor=#ccc align=center>";
		echo "<a href='planet.php?planet_id=" . $_GET['planet_id'] . "&player_id=" . $_GET['player_id'] . "'>Return to Marketplace</a>";
		echo "<td></tr>";
		echo "<tr><td valign=top>";
		// Show a list of ships to buy
		DisplayShipsForSale($_GET['player_id']);
		echo "<td valign=top>";
		// Show list of ships current player owns
		DisplayPlayerShips($_GET['player_id']);
		echo "</tr></table>";
	} else {
		echo "There is no shipyard on this planet.";
	}

	//outputDebugLog();

?>

</body>

</html>