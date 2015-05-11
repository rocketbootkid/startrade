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
	
	echo "<h2>Leaving [" . $current_planet_details[2] . ":" . $current_planet_details[0] . "] " . $current_planet_details[1] . "</h2>";
	
	// List planets
	ListPlanets('exclude', $current_planet_details[1], $_GET['player_id']);

	// Discover another galaxy
	echo "<a href=''>Search for other galaxies</a>";
	
?>


</body>

</html>