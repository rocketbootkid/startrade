<?php

	function marketPlace($planet_id) {
	
		// Generates the marketplace
	
		global $current_player;
	
		addToDebugLog("marketPlace(): Function Entry - supplied parameters: Planet ID: " . $planet_id);
	
		// Determine if this is the first visit to this planet
		// If so, get back the previous commodity details and vary slightly
		// If not, generate new commodity values
		
		$havewevisited = haveWeVisitedThisPlanet($planet_id);
		addToDebugLog("marketPlace(): Have we visited this planet: " . $havewevisited);
	
		if ($havewevisited == FALSE) {
			generateNewMarketplace($planet_id);
			addToDebugLog("marketPlace(): Generate a new marketplace");
			displayMarketplace($planet_id);
		} else {
			displayMarketplace($planet_id);
			addToDebugLog("marketPlace(): Rebuild the marketplace");
		}
		
	}
	
	function generateNewMarketplace($planet_id) {
	
		// Generate list of commodities to buy for the supplied planet
		
		addToDebugLog("generateNewMarketplace(): Function Entry - supplied parameters: Planet: " . $planet_id);
		
		global $current_player;		
		
		$sql = "SELECT * FROM startrade.commodity;";
		$result = search($sql);
		$rows = count($result);
		
		for ($c = 0; $c < $rows; $c++) {

			// Determine number of available units
			$select = rand(1, 4);
			if ($select <= 3) { // Determine if commodity is available
				$available_units = round(rand(10, 100), 0); // Generate available amount
				addToDebugLog("generateNewMarketplace(): " . $available_units . " units of " . $result[$c][1] . " available");
			} else {
				$available_units = 0;
				addToDebugLog("generateNewMarketplace(): Commodity not available: " . $result[$c][1]);
			}
			
			// If commodity is fuel, ensure there is some available
			if ($result[$c][0] == 16) { // Fuel
				$available_units = 100;
				addToDebugLog("generateNewMarketplace(): Ensured 100 units of fuel available");
			}
			
			
			// Generate Price
			$amount = round(rand(intval($result[$c][2]), $result[$c][3]), 0);
			if ($amount < $result[$c][2]) { // set price to minimum if random makes it zero
				$amount = $result[$c][2];
			}
			
			// Determine what type of planet we're on
			$planet_type = currentPlanetDetails($current_player);
			
			// If the planet type matches the commodity type, buff the commodity value by 10%
			$best_planet_type = $result[$c][3];
			if ($planet_type == $best_planet_type) {
				$amount = round($amount*1.1, 0);
			}	
			addToDebugLog("generateNewMarketplace(): " . $result[$c][1] . " price set at " . $amount);
					
			// Write current commodity values to the database
			$dml = "INSERT INTO startrade.marketplace (planet_id, commodity_id, commodity_unit_cost, commodity_units) VALUES (" . $planet_id . ", " . $result[$c][0] . ", " . $amount . ", " . $available_units . ");";
			$result_m = insert($dml);
			if ($result_m == TRUE) {
				addToDebugLog("generateNewMarketplace(): New commodity stored");
			} else {
				echo "ERROR!";
			}
			
		}
		
		echo "</table>";
	
	}
	
	function displayMarketplace($planet_id) {
	
		// Displays the contents of the planets marketplace
	
		addToDebugLog("displayMarketplace(): Function Entry - supplied parameters: Planet ID: " . $planet_id);		

		global $current_player;	
		
		$sql = "SELECT * FROM startrade.marketplace WHERE planet_id = " . $planet_id . ";";
		addToDebugLog("displayMarketplace(): Generated SQL: " . $sql);		
		
		// 0	marketplace_id
		// 1	planet_id
		// 2	commodity_id
		// 3	commodity_unit_cost
		// 4	commodity_units
		
		$result = search($sql);
		$rows = count($result);
		
		echo "\n\t<table border=1 cellspacing=0 cellpadding=3>";
		echo "\n\t\t<tr bgcolor=#82caff>";
		echo "\n\t\t\t<td>Commodities to Buy";
		echo "\n\t\t\t<td align=center>Cr";
		echo "\n\t\t\t<td align=center>U";
		echo "\n\t\t\t<td align=center>V<sub>U</sub>";
		echo "\n\t\t\t<td align=center colspan=4>Buy Max";
		echo "\n\t\t</tr>";
		
		for ($c = 0; $c < $rows; $c++) {

			if ($result[$c][4] == 0) { // If there are no units available, grey out the line
				echo "<tr bgcolor=#ddd>";
			} else {
				echo "<tr>";
			}
			$commodity_name = getCommodityDetail($result[$c][2], 'commodity_name');
			$best_planet_type = getCommodityDetail($result[$c][2], 'best_planet_type');
			echo "\n\t\t\t<td>" . $commodity_name . " (" . substr($best_planet_type, 0, 1) . ")"; // Description

			$commodity_min =  getCommodityDetail($result[$c][2], 'min_price');
			$commodity_max =  getCommodityDetail($result[$c][2], 'max_price');
			
			// If price is zero for some reason, or less than the minimum value for that commodity, reset to valid value
			if ($result[$c][3] == 0 || $result[$c][3] < $commodity_min) {
				addToDebugLog("generateNewMarketplace(): ERROR: Commodity ID " . $result[$c][2] . " has a price of zero");
				$amount = round(rand($commodity_min, $commodity_max), 0);
				addToDebugLog("generateNewMarketplace(): Setting commodity ID " . $result[$c][2] . "'s price to " . $amount);
			} else {
				$amount = $result[$c][3]; // Amount is current marketplace price
			}			
		
			// Highlight if price is good or bad
			$price_delta = $commodity_max - $commodity_min;
			if ($amount < ($commodity_min + (0.2*$price_delta))) { // Good for buying, within 20% of lowest price
				echo "\n\t\t\t<td align=center bgcolor=#81F781 title='" . $commodity_min . "'>";  // green
				addToDebugLog("generateNewMarketplace(): " . $amount . " is a good price for buying!");
			} elseif ($amount > ($commodity_max - (0.2*$price_delta))) { // Good for selling, within 20% of highest price
				echo "\n\t\t\t<td align=center bgcolor=#FE642E title='" . $commodity_max . "'>";
				addToDebugLog("generateNewMarketplace(): " . $amount . " is a bad price for buying!");
			} else {
				echo "\n\t\t\t<td align=center>";
			}
			
			// Alter price based on the number of available units
			$commodity_units = $result[$c][4];
			if ($commodity_units <= 10) {
				$amount = $amount * 1.2;
				addToDebugLog("generateNewMarketplace(): Not many units available, increasing price");
			} elseif (commodity_units >= 75) {
				$amount = $amount * 0.8;
				addToDebugLog("generateNewMarketplace(): Lots of units available, de`creasing price");
			}
			
			echo $amount; // Price
			
			// write price back to the database
			$dml = "UPDATE startrade.marketplace SET commodity_unit_cost = " . $amount . " WHERE marketplace_id = " . $result[$c][0] . ";";
			$resultdml = insert($dml);
			if ($resultdml == TRUE) {
				addToDebugLog("updateMarketplaces(): Commodity value updated");
			} else {
				addToDebugLog("updateMarketplaces(): Commodity value not updated");
			}
			
			echo "\n\t\t\t<td align=center>" . $result[$c][4]; // Units
			$available_units = $result[$c][4];
			
			$commodity_unit_volume = getCommodityDetail($result[$c][2], 'size'); // Get commodity
			echo "\n\t\t\t<td align=center>" . $commodity_unit_volume; // Unit Vol.
			
			// Determine max units, based on cargo space and available funds
			$current_funds = playerCurrentCredits($current_player);
			addToDebugLog("generateNewMarketplace(): Current funds: " . $current_funds);
			$remaining_cargo_space = remainingCargoSpace($current_player);
			addToDebugLog("generateNewMarketplace(): Remaining cargo space: " . $remaining_cargo_space);
			
			$units = 0;
			$cargo = 0;
			$cost = 0;
			while ($cost <= $current_funds && $cargo <= $remaining_cargo_space && $units <= $available_units) {

				$units++;
				$cost = $units*$amount;
				$cargo = $units*$commodity_unit_volume;

				addToDebugLog("generateNewMarketplace(): Commodity Checks for: " . $result[$c][1]);
				addToDebugLog("generateNewMarketplace(): Funds: Cost: " . $cost . ", Current funds: " . $current_funds);
				addToDebugLog("generateNewMarketplace(): Cargo: Cargo: " . $cargo . ", Available cargo space: " . $remaining_cargo_space);
				addToDebugLog("generateNewMarketplace(): Units: Units: " . $units . ", Available units: " . $available_units);

			}
			
			if ($units > 0) {
				$units = $units-1;
				$cost = $units*$amount;
				$cargo = $units*$commodity_unit_volume;
			}
			
			if ($available_units > 0 && $cost < $current_funds && $cargo <= $remaining_cargo_space && $units > 0) {
				addToDebugLog("generateNewMarketplace(): Can buy because;");
				addToDebugLog("generateNewMarketplace(): Funds: Cost: " . $cost . ", Current funds: " . $current_funds);
				addToDebugLog("generateNewMarketplace(): Cargo: Cargo: " . $cargo . ", Available cargo space: " . $remaining_cargo_space);
				addToDebugLog("generateNewMarketplace(): Units: Units: " . $units . ", Available units: " . $available_units);				
				echo "\n\t\t\t<td align=left><a href='transaction.php?planet_id=" . $planet_id . "&player_id=" . $current_player . "&txn=buy&marketplace_id=" . $result[$c][0] . "&units=" . $units . "&unit_cost=" . $amount . "&commodity_id=" . $result[$c][2] . "' target='_blank'>Max</a>"; // Max

				echo "\n\t\t\t<td align=center>" . $units . "u<td align=center>" . $cost . "Cr<td align=center>" . $cargo . "V<sub>u</sub>";
			} else {
				echo "\n\t\t\t<td colspan=4 align=center>-";
				addToDebugLog("generateNewMarketplace(): Can't buy for one of the following reasons;");
				addToDebugLog("generateNewMarketplace(): Funds: Cost: " . $cost . ", Current funds: " . $current_funds);
				addToDebugLog("generateNewMarketplace(): Cargo: Cargo: " . $cargo . ", Available cargo space: " . $remaining_cargo_space);
				addToDebugLog("generateNewMarketplace(): Units: Units: " . $units . ", Available units: " . $available_units);
			}
			
			echo "\n\t\t</tr>";
			
		}
		
		echo "\n\t</table>";	
	
	}
	
	function updateMarketplaces() {

		// Updates the price and amount for all commodities in all marketplaces
		
		addToDebugLog("updateMarketplaces(): Function Entry");	
		
		$sql = "SELECT marketplace_id, commodity_unit_cost, commodity_units FROM startrade.marketplace;";
		addToDebugLog("updateMarketplaces(): Constructed query: " . $sql);	
		$result = search($sql);
		$rows = count($result);
		
		for ($c = 0; $c < $rows; $c++) {
		
			$marketplace_id = $result[$c][0];	
			$commodity_unit_cost = $result[$c][1];	
			$commodity_units = $result[$c][2];	

			// Update commodity cost to simulate market fluctuations
			
			$modifier = rand(95, 105)/100;
			addToDebugLog("updateMarketplaces(): Commodity Unit cost modifier: " . $modifier);	
			$new_price = round($modifier * $commodity_unit_cost, 0);
			addToDebugLog("updateMarketplaces(): Commodity Unit new price: " . $new_price);	

			// Update commodity amount to simulate production
			
			if ($commodity_units == 0) {
				$new_units = rand(0, 50);
			} elseif ($commodity_units >= 20 && $commodity_units < 40) {
				$new_units = rand(0, 20);
			} elseif ($commodity_units >= 40 && $commodity_units < 60 ) {
				$new_units = rand(0, 10);
			} elseif ($commodity_units > 80) {
				$new_units = 0;
			}
			addToDebugLog("updateMarketplaces(): Commodity amount modifier: " . $new_units);	
			
			$dml = "UPDATE startrade.marketplace SET commodity_unit_cost = " . $new_price . ", commodity_units = commodity_units + " . $new_units . " WHERE marketplace_id = " . $marketplace_id . ";";
			$resultdml = insert($dml);
			if ($resultdml == TRUE) {
				addToDebugLog("updateMarketplaces(): Commodity value updated");
			} else {
				addToDebugLog("updateMarketplaces(): Commodity value not updated");
			}
			
		}
	
	}
	
	function updateMarketplaceFuel() {
	
		// Updates the price for all commodities in all marketplaces
		
		addToDebugLog("updateMarketplaceFuel(): Function Entry");		
	
		// Only Industrial planets have fuel
		
		$sql = "SELECT planet_id FROM startrade.planets WHERE planet_type = 'Industrial';";
		addToDebugLog("updateMarketplaceFuel(): Constructed query: " . $sql);	
		$result = search($sql);
		$rows = count($result);
		addToDebugLog("updateMarketplaceFuel(): Industrial planets found: " . $rows);	
		
		for ($c = 0; $c < $rows; $c++) {		
			// Get existing amount of fuel on the planet
			$sql_existing = "SELECT commodity_units FROM startrade.marketplace WHERE planet_id = " . $result[$c][0] . " AND commodity_id = 16;";
			addToDebugLog("updateMarketplaceFuel(): Generated SQL: " . $sql);
			
			$result_existing = search($sql_existing);
			$rows_existing = count($result_existing);			
			//echo "<p>Planet ID: " . $result[$c][0] . ", Existing rows: " . $rows_existing . ", Fuel: " . $rows_existing[0][0];
			
			if ($rows_existing == 0) {
				//echo "Insert a new row";
				addToDebugLog("updateMarketplaceFuel(): Fuel commodity not found");	
				// If there is no fuel entry for the planet, add some
				$commodity_unit_cost = rand(500, 1000);
				$commodity_units = rand(50, 100);
				$dml_existing = "INSERT INTO startrade.marketplace (planet_id, commodity_id, commodity_unit_cost, commodity_units) VALUES (" . $result[$c][0] . ", 16, " . $commodity_unit_cost . ", " . $commodity_units . ");";
				$result_existing = insert($dml_existing);
				if ($result_existing == TRUE) {
					addToDebugLog("updateMarketplaceFuel(): Fuel data added");
				} else {
					addToDebugLog("updateMarketplaceFuel(): Fuel data not added");
				}
			
			} elseif ($rows_existing == 1) {
				//echo "Update existing row";
				addToDebugLog("updateMarketplaceFuel(): Fuel commodity found, currently " . $result_existing[0][0] . " units");	
				// Add 10, or round up to 100
				if ($result_existing[0][0] >= 95) {
					$dml_update = "UPDATE startrade.marketplace SET commodity_units = 100 WHERE planet_id = " . $result[$c][0] . " AND commodity_id = 16;";
					addToDebugLog("updateMarketplaceFuel(): Fuel commodity found, updating to 100 units");
				} else {
					$dml_update = "UPDATE startrade.marketplace SET commodity_units = commodity_units + 5 WHERE planet_id = " . $result[$c][0] . " AND commodity_id = 16;";
					addToDebugLog("updateMarketplaceFuel(): Fuel commodity found, increasing by 5 units");
				}
				$result_update = insert($dml_update);
				if ($result_update == TRUE) {
					addToDebugLog("updateMarketplaceFuel(): Fuel data updated");
				} else {
					addToDebugLog("updateMarketplaceFuel(): Fuel data not updated");
				}			
			} else {
				//echo "Do nothing; too many rows!";
			}
		}
	}
	
	function getMarketplaceDetail($planet_id, $commodity_id, $attribute) {
	
		// Gets details about the chosen commodity on selected planet

		addToDebugLog("getMarketplaceDetail(): Function Entry - supplied parameters: Commodity ID: " . $commodity_id . ", Commodity attribute: " . $attribute);	
	
		// [0] marketplace_id
		// [1] planet_id
		// [2] commodity_id
		// [3] commodity_unit_cost
		// [4] commodity_units
 	
		$sql = "SELECT " . $attribute . " FROM startrade.marketplace WHERE commodity_id = '" . $commodity_id . "' AND planet_id = '" . $planet_id . "';";
		addToDebugLog("getMarketplaceDetail(): Constructed query: " . $sql);
		
		$result = search($sql);
		$marketplace_detail = $result[0][0];
		
		addToDebugLog("getMarketplaceDetail(): Commodity " . $commodity_id . "'s " . $attribute . " on planet " . $planet_id . ": " . $marketplace_detail);
		
		return $marketplace_detail;
	
	}
	
?>