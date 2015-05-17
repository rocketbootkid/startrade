<html>



<?php

	include 'functions/debug_functions.php';
	include 'functions/startrade_functions.php';
	include 'functions/mysql_functions.php';
	include 'functions/shipyard_functions.php';
	include 'functions/marketplace_functions.php';
	
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
	
	if ($current_planet_name != $new_planet_name) {
	
		// Determine if we've been here before
		$havewevisited = haveWeVisitedThisPlanet($_GET['planet_id']);
		if ($havewevisited == FALSE) { // If we haven't, generate its marketplace
			generateNewMarketplace($_GET['planet_id']);
		}
		
		// Update players current fuel
		updateCurrentFuel($_GET['player_id'], $_GET['planet_id'], $current_planet_id);

		// Update prices across all marketplaces
		updateMarketplaces();
		
		// Update fuel across all marketplaces
		updateMarketplaceFuel();
			
	}
		
	//outputDebugLog();
	
	echo "<script>window.location.href = 'planet.php?planet_id=" . $_GET['planet_id'] . "&player_id=" . $_GET['player_id']. "';</script>";

?>

</html>