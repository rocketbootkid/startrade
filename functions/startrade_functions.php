<?php

	// ********************************************************************************************************************************************
	// ********************************************************* DISPLAY FUNCTIONS ****************************************************************
	// ********************************************************************************************************************************************	
	
	// currentPlanetDetails()	Returns name of the planet the player is currently on.
	// currentSystem()			Returns the player's current system
	// updateCurrentPlanet()	Updates the current planet the player is on
	// updateCurrentFuel()		Reduce player fuel based on what planet they're going to
	// currentFuel()			Returns the current amount of fuel the player has
	// planetTooFar()			Determines if the player is able to travel to a planet
	// getSystemId()			Determines what system a planet is in
	// marketPlace()			Generates the list of commodities available in a marketplace
	// cargo()					Lists the commodities, fuel and money that a player has
	// getShipCargoCapacity()	Gets the player's ship's cargo capacity
	// getCommodityDetail()		Gets details about the chosen commodity

	function currentPlanetDetails($player_id) {
	
		// Returns the details of the planet the player is currently on.
	
		addToDebugLog("currentPlanetDetails(): Function Entry - supplied parameters: Player ID: " . $player_id);	
	
		$sql = "SELECT * FROM startrade.planets, startrade.player WHERE player.player_id = " . $player_id . " AND player.current_planet = planets.planet_id;";
		addToDebugLog("currentPlanetDetails(): Constructed query: " . $sql);
		
		$result = search($sql);
		$planet_details = $result[0][0] . "," . $result[0][1] . "," . $result[0][2] . "," . $result[0][3];
		
		// [0]	Planet ID
		// [1]	Planet Name
		// [2]	System ID
		// [3]	Planet Type
		
		addToDebugLog("currentPlanetDetails(): Current planet details: " . $planet_details);
		
		return $planet_details;
		
	}
	
	function currentSystem($player_id) {
	
		// Returns the player's current system
	
		addToDebugLog("currentSystem(): Function Entry - supplied parameters: Player ID: " . $player_id);	
	
		$sql = "SELECT system_id FROM startrade.planets, startrade.player WHERE player.player_id = " . $player_id . " AND player.current_planet = planets.planet_id;";
		addToDebugLog("currentSystem(): Constructed query: " . $sql);
		
		$result = search($sql);
		$system_id = $result[0][0];
		
		addToDebugLog("currentSystem(): Current system: " . $system_id);
		
		return $system_id;		
	
	}
	
	function updateCurrentPlanet($player_id, $planet_id) {
	
		// Updates the current planet the player is on
	
		addToDebugLog("updateCurrentPlanet(): Function Entry - supplied parameters: Player ID: " . $player_id . ", Planet ID: " . $planet_id);	

		// Change players current planet
		$dml = "UPDATE startrade.player SET current_planet = " . $planet_id . " WHERE player_id = " . $player_id . ";";
		addToDebugLog("updateCurrentPlanet(): Constructed query: " . $dml);	
		$result = insert($dml);
		if ($result[0][0] == TRUE) {
			addToDebugLog("- updateCurrentPlanet(): Current planet updated");
		}
	
	}
	
	function updateCurrentFuel($player_id, $target_planet_id, $current_planet_id) {
		
		// Reduce player fuel based on what planet they're going to
		
		// $planet_id:			Character Id
		// $target_planet_id:	Planet that character is going to
		// $current_planet_id:	Planet that characters is on
		
		addToDebugLog("updateCurrentFuel(): Function Entry - supplied parameters: Player ID: " . $player_id . ", Target Planet ID: " . $target_planet_id . ", Current Planet ID: " . $current_planet_id);
	
		global $insystem_fuel_cost;
		global $outsystem_fuel_cost;
		
		// Check if it's just a page refresh, staying on the same planet
		addToDebugLog("updateCurrentFuel(): Current Planet ID: " . $current_planet_id . ", Target Planet ID: " . $target_planet_id);
		
		if ($target_planet_id != $current_planet_id) {
		
			// Get current system id
			$sql = "SELECT system_id from startrade.planets WHERE planet_id = '" . $current_planet_id . "';";
			$result = search($sql);
			$current_system_id = $result[0][0];
			addToDebugLog("updateCurrentFuel(): Current system: " . $current_system_id);
			
			// Get target system id
			$sql = "SELECT system_id from startrade.planets WHERE planet_id = " . $target_planet_id . ";";
			$result = search($sql);
			$target_system_id = $result[0][0];
			addToDebugLog("updateCurrentFuel(): Target system: " . $target_system_id);

			$fuel = currentFuel($player_id);
			addToDebugLog("- updateCurrentFuel(): Current fuel state: " . $fuel);
			$number_of_ships = playerShips($player_id);
			addToDebugLog("- updateCurrentFuel(): Current player ships: " . $number_of_ships);
			
			// Consume fuel
			if ($current_system_id == $target_system_id) {
				// New planet in same system; uses 1 fuel
				addToDebugLog("updateCurrentFuel(): Travelling in-system");
				
				// New fuel state
				$flotilla_fuel = $number_of_ships*$insystem_fuel_cost;
				$fuel = $fuel - $flotilla_fuel;
				addToDebugLog("- updateCurrentFuel(): New fuel state: " . $fuel);
				
				// Update fuel state
				$dml = "UPDATE startrade.player SET remaining_fuel = " . $fuel . " WHERE player_id = " . $player_id . ";";
				addToDebugLog("updateCurrentFuel(): Constructed query: " . $dml);
				$result = insert($dml);
				if ($result[0][0] == TRUE) {
					addToDebugLog("- updateCurrentFuel(): Travel within system - Remaining fuel reduced by " . $insystem_fuel_cost);
				}	
			} else {
				// New planet in different system, uses 2 fuel
				addToDebugLog("updateCurrentFuel(): Travelling out-system");
				
				// New fuel state
				$flotilla_fuel = $number_of_ships*$outsystem_fuel_cost;
				$fuel = $fuel - $flotilla_fuel;
				addToDebugLog("- updateCurrentFuel(): New fuel state: " . $fuel);
				
				$dml = "UPDATE startrade.player SET remaining_fuel = " . $fuel . " WHERE player_id = " . $player_id . ";";
				addToDebugLog("updateCurrentFuel(): Constructed query: " . $dml);
				$result = insert($dml);
				if ($result[0][0] == TRUE) {
					addToDebugLog("- updateCurrentFuel(): Travel between systems - Remaining fuel reduced by " . $outsystem_fuel_cost);
				}		
			
			}
		
		} else {
			addToDebugLog("- updateCurrentFuel(): Travel between systems - Remaining fuel reduced by " . $outsystem_fuel_cost);
		}
			
	}

	function currentFuel($player_id) {
	
		// Returns the current amount of fuel the player has
	
		addToDebugLog("currentFuel(): Function Entry");	
	
		$sql = "SELECT remaining_fuel FROM startrade.player WHERE player.player_id = " . $player_id . ";";
		addToDebugLog("currentFuel(): Constructed query: " . $sql);
		
		$result = search($sql);
		$remaining_fuel = $result[0][0];
		
		addToDebugLog("currentFuel(): Fuel remaining: " . $remaining_fuel);
		
		return $remaining_fuel;
		
	}
	
	function planetTooFar($player_id, $planet_id) {	
	
		// Determines if the player is able to travel to a planet
	
		addToDebugLog("planetTooFar(): Function Entry - supplied parameters: Player ID: " . $player_id . ", Planet ID: " . $planet_id);

		global $insystem_fuel_cost;
		global $outsystem_fuel_cost;
		
		// Current Planet
		$current_planet_details = currentPlanetDetails($player_id); // planet_id, planet_name, system_id
		$current_planet = explode(",", $current_planet_details);
		$current_planet = $current_planet[1];
		$current_system_id = currentSystem($player_id);
		
		// Target Planet
		$target_planet = $planet_id;
		$target_system_id = getSystemId($target_planet);
		
		addToDebugLog("planetTooFar(): Planets: Current: " . $current_planet . ", Target: " . $target_planet);
		addToDebugLog("planetTooFar(): Systems: Current: " . $current_system_id . ", Target: " . $target_system_id);
		
		// Determine Range
		if ($current_system_id == $target_system_id) {
			$range = $insystem_fuel_cost;
			addToDebugLog("planetTooFar(): In-system flight; requires " . $insystem_fuel_cost . " fuel");
		} else {
			$range = $outsystem_fuel_cost;
			addToDebugLog("planetTooFar(): Out-system flight; requires " . $outsystem_fuel_cost . " fuel");
		}
		
		// Determine Fuel
		$fuel = currentFuel($player_id);
		addToDebugLog("planetTooFar(): Player current fuel: " . $fuel);
		
		// Is it too far?
		if ($range > $fuel) {
			$isittoofar = TRUE;
			addToDebugLog("planetTooFar(): Player has " . $fuel . " fuel, but needs " . $range . ". They can't make it!");
		} else {
			$isittoofar = FALSE;
			addToDebugLog("planetTooFar(): Player has " . $fuel . " fuel, and only needs " . $range . ". They can make it");
		}
	
		return $isittoofar;
	
	}
	
	function getSystemId($planet_name) {
	
		// Determines what system a planet is in
	
		addToDebugLog("getSystemId(): Function Entry - supplied parameters: Planet: " . $planet_name);	
	
		$sql = "SELECT system_id FROM startrade.planets WHERE planet_name = '" . $planet_name . "';";
		addToDebugLog("getSystemId(): Constructed query: " . $sql);
		
		$result = search($sql);
		$system_id = $result[0][0];
		
		addToDebugLog("getSystemId(): System ID: " . $system_id);
		
		return $system_id;		

	}
	
	function cargo($player_id) {
	
		// Lists the commodities, fuel and money that a player has
	
		addToDebugLog("cargo(): Function Entry - supplied parameters: Player ID: " . $player_id);	
	
		echo "\n\t<table border=1 cellspacing=0 cellpadding=3>";
		echo "\n\t\t<tr bgcolor=#82caff>";
		echo "\n\t\t\t<td>Commodities to Sell";
		echo "\n\t\t\t<td align=center>Units";
		echo "\n\t\t\t<td align=center>Volume";
		echo "\n\t\t\t<td align=center>Bought At";
		echo "\n\t\t\t<td align=center>Price Here";
		echo "\n\t\t\t<td align=center>Best Price";
		echo "\n\t\t\t<td align=center>Sell All";
		
		echo "\n\t\t</tr>";
	
		// Player Credits
		
		$sql = "SELECT current_funds FROM startrade.player WHERE player_id = '" . $player_id . "';";
		addToDebugLog("cargo(): Constructed query: " . $sql);
		$result = search($sql);
		$funds = $result[0][0];
		addToDebugLog("getSystemId(): Current funds for Player " . $player_id . ": " . $funds);
		echo "\n\t\t<tr>\n\t\t\t<td>Credits\n\t\t\t<td align=center>" . $funds . "\n\t\t\t<td colspan=5 align=center>-\n\t\t</tr>";
		
		// Player cargo

		// Determine ship capacity

		$cargo = getShipCargoCapacity($player_id);
		
		// List ship's cargo
		$sql = "SELECT cargohold.commodity_id, commodity_name, amount, bought_for FROM startrade.cargohold, startrade.commodity WHERE cargohold.player_id = '" . $player_id . "' AND cargohold.commodity_id = commodity.commodity_id;";
		addToDebugLog("cargo(): Constructed query: " . $sql);
		$result = search($sql);
		$rows = count($result);
		addToDebugLog("cargo(): Player has " . $rows . " different types of cargo in their hold.");
		$hold_cargo_volume = 0;
		if ($rows > 0) {
			for ($c = 0; $c < $rows; $c++) {
				$best_planet_type = getCommodityDetail($result[$c][0], 'best_planet_type');
				echo "\n\t\t<tr>";
				echo "\n\t\t\t<td>" . $result[$c][1] . " (" . substr($best_planet_type, 0, 1) . ")"; // Commodity
				echo "\n\t\t\t<td align=center>" . $result[$c][2]; // Amount in hold
				
				// Get cargo unit volume
				$commodity_id = $result[$c][0];
				$cargo_unit_volume = getCommodityDetail($commodity_id, 'size');
				$cargo_volume = $result[$c][2]*$cargo_unit_volume;
				
				// Update cargo volume running total
				$hold_cargo_volume = $hold_cargo_volume + $cargo_volume;
				addToDebugLog("cargo(): Hold currently contains " . $hold_cargo_volume . " units");
				
				echo "\n\t\t\t<td align=center >" . $cargo_volume; // Cargo Volume
				echo "\n\t\t\t<td align=center>" . $result[$c][3]; // Price Bought For
				
				// Identify price of commodity at this planet
				$price_here = getMarketplaceDetail($_GET['planet_id'], $commodity_id, "commodity_unit_cost");
				$price_delta = $price_here - $result[$c][3];
				if ($price_delta > 0) {
					$color = "#81F781";
				} else {
					$color = "FE642E";
				}
				echo "\n\t\t\t<td align=center bgcolor=" . $color . ">" . $price_here . " (" . $price_delta . ")"; // Price Here
				
				$best_price = getBestPrice($commodity_id); // [0] is Price, [1] is Planet Name
				// Hide part of the planet name where best price is, either system or planet
				$best_price_planet = explode(" ", $best_price[0][1]);
				$option = round(rand(0,1));
				if ($option == 0) {
					$best_price_planet_display = "??? " . $best_price_planet[1];
				} else {
					$best_price_planet_display = $best_price_planet[0] . " ???";
				}

				echo "\n\t\t\t<td align=center>" . $best_price[0][0] . " (" . $best_price_planet_display . ")"; // Best Price
				echo "\n\t\t\t<td align=center><a href='transaction.php?planet_id=" . $_GET['planet_id'] . "&player_id=" . $player_id . "&txn=sell&units=" . $result[$c][2] . "&commodity_id=" . $commodity_id . "' target='_blank' onclick='setTimeout(location.reload(true);,500);'>Sell All</a>"; // Sell All
				
				echo "\n\t\t</tr>";
			}
			$remaining_cargo_space = $cargo - $hold_cargo_volume;
		} else {
			$remaining_cargo_space = $cargo;
			echo "\n\t\t<tr>\n\t\t\t<td colspan=7>No cargo\n\t\t</tr>";
		}
		
		// Player Fuel

		$sql = "SELECT remaining_fuel FROM startrade.player WHERE player_id = '" . $player_id . "';";
		addToDebugLog("cargo(): Constructed query: " . $sql);
		$result = search($sql);
		$remaining_fuel = $result[0][0];
		addToDebugLog("getSystemId(): Remaining fuel for Player " . $player_id . ": " . $remaining_fuel);
		echo "\n\t\t<tr>\n\t\t\t<td>Remaining Fuel\n\t\t\t<td align=center>" . $remaining_fuel . "\n\t\t\t<td colspan=5 align=center>-\n\t\t</tr>";	
		
		// Remaining cargo space
		
		echo "\n\t\t<tr>\n\t\t\t<td>Remaining Cargo space\n\t\t\t<td align=center>" . $remaining_cargo_space . "V<sub>u</sub>\n\t\t\t<td colspan=5 align=center>-\n\t\t</tr>";
	
		echo "\n\t</table>";
	
	}
	
	function playerCurrentCredits($player_id) {

		// Determine players current credit level
		
		addToDebugLog("playerCurrentCredits(): Function Entry - supplied parameters: Player ID: " . $player_id);
	
		$sql = "SELECT current_funds FROM startrade.player WHERE player_id = '" . $player_id . "';";
		addToDebugLog("playerCurrentCredits(): Constructed query: " . $sql);
		$result = search($sql);
		$funds = $result[0][0];
		
		return $funds;
	
	}
	
	function remainingCargoSpace($player_id) {
	
		// Determine ships space cargo capacity
		
		addToDebugLog("remainingCargoSpace(): Function Entry - supplied parameters: Player ID: " . $player_id);	

		$cargo = getShipCargoCapacity($player_id);
		
		// List ship's cargo
		$sql = "SELECT commodity_name, amount FROM startrade.cargohold, startrade.commodity WHERE cargohold.player_id = '" . $player_id . "' AND cargohold.commodity_id = commodity.commodity_id;";
		addToDebugLog("cargo(): Constructed query: " . $sql);
		$result = search($sql);
		$rows = count($result);
		$hold_cargo_volume = 0;
		
		if ($rows > 0) {
			for ($c = 0; $c < $rows; $c++) {
				
				// Get cargo unit volume
				$commodity_id = getCommodityID($result[$c][0]);
				$cargo_unit_volume = getCommodityDetail($commodity_id, 'size');
				$cargo_volume = $result[$c][1]*$cargo_unit_volume;
				
				// Update cargo volume running total
				$hold_cargo_volume = $hold_cargo_volume + $cargo_volume;
				addToDebugLog("remainingCargoSpace(): Hold currently contains " . $hold_cargo_volume . " units");
			}
			$remaining_cargo_space = $cargo - $hold_cargo_volume;
		} else {
			$remaining_cargo_space = $cargo;
		}
		
		return $remaining_cargo_space;
	
	}
	
	function getShipCargoCapacity($player_id) {
	
		// Gets the player's ship's cargo capacity
	
		addToDebugLog("getShipCargoCapacity(): Function Entry - supplied parameters: Player ID: " . $player_id);	
	
		$sql = "SELECT SUM(ship_cargo) FROM startrade.ships WHERE player_id = " . $player_id . ";";
		addToDebugLog("getShipCargoCapacity(): Constructed query: " . $sql);
		
		$result = search($sql);
		$ship_cargo = $result[0][0];
		
		addToDebugLog("getShipCargoCapacity(): Player " . $player_id . "'s ships have a cargo capacity of " . $ship_cargo);
		
		return $ship_cargo;		
	
	}
	
	function getCommodityDetail($commodity_id, $attribute) {
	
		// Gets details about the chosen commodity

		addToDebugLog("getCommodityDetail(): Function Entry - supplied parameters: Commodity ID: " . $commodity_id . ", Commodity attribute: " . $attribute);	
	
		$sql = "SELECT " . $attribute . " FROM startrade.commodity WHERE commodity_id = '" . $commodity_id . "';";
		addToDebugLog("getCommodityDetail(): Constructed query: " . $sql);
		
		$result = search($sql);
		$commodity_detail = $result[0][0];
		
		addToDebugLog("getCommodityDetail(): Commodity " . $commodity_id . "'s " . $attribute . ": " . $commodity_detail);
		
		return $commodity_detail;
	
	}
	
	function getCommodityID($commodity_name) {
	
		// Returns commodity id for supplied commodity
		
		addToDebugLog("getCommodityID(): Function Entry - supplied parameters: Commodity name: " . $commodity_name);	
	
		$sql = "SELECT commodity_id FROM startrade.commodity WHERE commodity_name = '" . $commodity_name . "';";
		addToDebugLog("getCommodityDetail(): Constructed query: " . $sql);
		
		$result = search($sql);
		$commodity_id = $result[0][0];
		
		addToDebugLog("getCommodityID(): Commodity " . $commodity_id . "'s ID: " . $commodity_id);
		
		return $commodity_id;		
	
	}
	
	function haveWeVisitedThisPlanet($planet_id) {
	
		// Determines if we've visited this planet before by looking for saved commodity information
		
		addToDebugLog("haveWeVisitedThisPlanet(): Function Entry - supplied parameters: Planet ID: " . $planet_id);

		$sql = "SELECT marketplace_id FROM startrade.marketplace WHERE planet_id = " . $planet_id . ";";
		addToDebugLog("haveWeVisitedThisPlanet(): Constructed query: " . $sql);
		
		$result = search($sql);
		$count = count($result);
		addToDebugLog("haveWeVisitedThisPlanet(): Found Commodity rows: " . $count);
		
		if ($count == 0) {
			$havewevisited = FALSE;
		} else {
			$havewevisited = TRUE;
		}
		addToDebugLog("haveWeVisitedThisPlanet(): Have we visited this planet before: " . $havewevisited);
	
		return $havewevisited;
	
	}
	
	function getBestPrice($commodity_id) {

		// Finds best price for supplied commodity on visited planets
		
		addToDebugLog("getBestPrice(): Function Entry - supplied parameters: Commodity ID: " . $commodity_id);

		$sql = "SELECT commodity_unit_cost, planet_name FROM startrade.marketplace, startrade.planets WHERE marketplace.commodity_id = " . $commodity_id . " AND marketplace.planet_id = planets.planet_id ORDER BY commodity_unit_cost DESC LIMIT 1;";
		addToDebugLog("sell(): Constructed query: " . $sql);	
		$result = search($sql);
		addToDebugLog("getBestPrice(): Function Entry - Price: " . $result[0][0] . ", Planet: " . $result[0][1]);
		
		
		return $result;
	
	}

?>