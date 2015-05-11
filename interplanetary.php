<html>



<?php

	include 'functions/debug_functions.php';
	include 'functions/startrade_functions.php';
	include 'functions/mysql_functions.php';
	include 'functions/shipyard_functions.php';
	
	// Get players current planet
	$current_planet_details = currentPlanetDetails($_GET['player_id']);
	$current_planet_details = explode(",", $current_planet_details);
	// [0]	Planet ID
	// [1]	Planet Name
	// [2]	System ID
	// [3]	Planet Type
	$current_planet_id = $current_planet_details[0];
	$current_planet_name = $current_planet_details[1];
	
	// Update players current planet
	updateCurrentPlanet($_GET['player_id'], $_GET['planet_id']);

	// Get players new planet
	$new_planet_details = currentPlanetDetails($_GET['player_id']);
	$new_planet_details = explode(",", $new_planet_details);
	// [0]	Planet ID
	// [1]	Planet Name
	// [2]	System ID
	// [3]	Planet Type
	$new_planet_name = $new_planet_details[1];

	//echo "<head><title>" . $current_planet_name . " >>> " . $new_planet_name . "</title></head><body>";
	//echo "Leaving " . $current_planet_name . ", travelling to " . $new_planet_name;
	
	if ($current_planet_name != $new_planet_name) {
	
		// Update players current fuel
		updateCurrentFuel($_GET['player_id'], $_GET['planet_id'], $current_planet_id);

		// Update prices across all marketplaces
		updateMarketplaces();
		
		// Update fuel across all marketplaces
		updateMarketplaceFuel();
	
	}
		
	//echo "<p><a href='planet.php?planet_id=" . $_GET['planet_id'] . "&player_id=" . $_GET['player_id']. "'>Land on " . $new_planet_name . "</a>";
		
	//outputDebugLog();
	
	echo "<script>window.location.href = 'planet.php?planet_id=" . $_GET['planet_id'] . "&player_id=" . $_GET['player_id']. "';</script>";

?>

</body>

</html>