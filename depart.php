<html>

<head>
	<title>Galaxy | Choose Destination</title>
</head>

<body>

<?php

	include 'functions/debug_functions.php';
	include 'functions/mysql_functions.php';
	include 'functions/startrade_functions.php';
	include 'functions/galaxy_functions.php';
	
	global $current_player;
	$current_player = $_GET['player_id'];

	// Get players current planet
	$current_planet_details = currentPlanetDetails($_GET['player_id']);
	$current_planet_details = explode(",", $current_planet_details);
	
	// 0	planet_id
	// 1	planet_name
	// 2	system_id
	// 3	system_name

	// Get current galaxy for display and to limit the displayed systems
	$galaxy_details = galaxyDetails($current_planet_details[2]);
	$galaxy_name = $galaxy_details[0][1];
	$galaxy_id = $galaxy_details[0][0];
	
	echo "<h2>Leaving [" . $galaxy_id . ":" . $current_planet_details[2] . ":" . $current_planet_details[0] . "] " . $current_planet_details[1] . " in the " . $galaxy_name . " galaxy</h2>";
	
	// List planets in current galaxy
	ListPlanets('exclude', $current_planet_details[1], $_GET['player_id'], $galaxy_id);

	// List other galaxies
	
	$sql = "SELECT distinct(galaxy_id) FROM startrade.systems WHERE galaxy_id != " . $galaxy_id . ";";
	addToDebugLog("sell(): Constructed query: " . $sql);	
	$result = search($sql);
	$rows = count($result);
	
	echo "<p>There are " . $rows . " other known galaxies.";
	
	for ($g = 0; $g < $rows; $g++) {	
		ListPlanets('galaxylist','',$_GET['player_id'],$result[$g][0]);	
	}

	//outputDebugLog();
	
?>


</body>

</html>