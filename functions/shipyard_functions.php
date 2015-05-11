<?php
	
	function DisplayShipsForSale($player_id) {

		// Shows available ships to buy
		
		addToDebugLog("buyShip(): Function Entry - Player ID: " . $player_id);
		
		// Ship name, cargo size and price (based on cargo size)

		echo "\n\t<table border=1 cellspacing=0 cellpadding=3>";
		echo "\n\t\t<tr bgcolor=#82caff>";
		echo "\n\t\t\t<td>Ship Name";
		echo "\n\t\t\t<td align=center>Cargo Volume";
		echo "\n\t\t\t<td align=center>Price";
		echo "\n\t\t\t<td align=center>Buy";
		echo "\n\t\t</tr>";
	
		// Determine how many ships to generate
		$number_ships = rand(3, 7);
		
		// Determine player's current cash
		$player_money = playerCurrentCredits($player_id);
		
		for ($s = 0; $s < $number_ships; $s++) {
		
			$cargo_size = rand(2, 30)*100;
			$price = rand(3000, 4000)*$cargo_size;
			$name_modifier = $cargo_size+rand(0, 99);
			$name = generateShipName() . "-" . $name_modifier;
		
			echo "\n\t\t<tr>";
			echo "\n\t\t\t<td id='name'>" . $name;
			echo "\n\t\t\t<td id='cargo' align=center>" . $cargo_size . "V<sub>u</sub>";
			echo "\n\t\t\t<td id='price' align=center>" . $price . "Cr";
			echo "\n\t\t\t<td id='buy' align=center>";
			if ($price <= $player_money) {
				echo "<a href='shipyard_transactions.php?player_id=" . $player_id . "&name=" . $name . "&price=" . $price . "&cargo=" . $cargo_size . "&tradein=" . $trade_in_price . "&txn=buy' target='_blank'>Buy</a>";
			}
			echo "\n\t\t</tr>";
		}
		
		echo "\n\t</table>";
	
	}
	
	function DisplayPlayerShips($player_id) {
	
		// This function displays a list of ships in the player's flotilla
	
		addToDebugLog("buyShip(): Function Entry - Player ID: " . $player_id);
		
		// Ship name, cargo size and price (based on cargo size)

		echo "\n\t<table border=1 cellspacing=0 cellpadding=3>";
		echo "\n\t\t<tr bgcolor=#82caff>";
		echo "\n\t\t\t<td>Ship Name";
		echo "\n\t\t\t<td align=center>Cargo Volume";
		echo "\n\t\t\t<td align=center>Price";
		echo "\n\t\t\t<td align=center>Sell";
		echo "\n\t\t</tr>";
	
		$sql = "SELECT * FROM startrade.ships WHERE player_id = " . $player_id . ";";
		addToDebugLog("DisplayPlayerShips(): Constructed query: " . $sql);	
		$result = search($sql);

		$number_ships = count($result);
		for ($s = 0; $s < $number_ships; $s++) {
		
			$sale_price = rand(1500, 2500)*$result[$s][2];
		
			echo "\n\t\t<tr>";
			echo "\n\t\t\t<td id='name'>" . $result[$s][1];
			echo "\n\t\t\t<td id='cargo' align=center>" . $result[$s][2] . "V<sub>u</sub>";
			echo "\n\t\t\t<td id='price' align=center>" . $sale_price . "Cr";
			echo "\n\t\t\t<td id='sell' align=center>";
			
			// Need to determine if player has sufficient cargo space in remaining vessels to sell
			$total_cargo_capacity = getShipCargoCapacity($player_id);
			$remaining_capacity = $total_cargo_capacity-$result[$s][2];
			$current_cargo = currentCargo($player_id);
			
			if ($current_cargo <= $remaining_capacity) {
				echo "<a href='shipyard_transactions.php?player_id=" . $player_id . "&ship_id=" . $result[$s][0] . "&sale_price=" . $sale_price . "&txn=sell' target='_blank'>Sell</a>";
			} else { // Too much cargo to sell ship
				echo "-";
			}
			echo "\n\t\t</tr>";
		}
		
		echo "\n\t</table>";	

	}
	
	function generateShipName() {

		// Shows available ships to buy
		
		addToDebugLog("generateShipName(): Function Entry");	
	
		$syllables = rand(2, 4);
		$name = "";
		
		for ($a = 0; $a < $syllables; $a++) {

			$consonants = array("b", "c", "d", "f", "g", "h", "j", "k", "l", "m", "n", "p", "q", "r", "s", "t", "v", "w", "x", "y", "z"); 
			$consonant = $consonants[rand(0, 20)];
			
			$vowels = array("a", "e", "i", "o", "u");
			$vowel = $vowels[rand(0, 4)];
			
			$name = $name . $consonant . $vowel;

		}
		
		$name = ucfirst($name);
		
		addToDebugLog("generateShipName(): Generated name: " . $name);	
	
		return $name;
	
	}
	
	function buyShip($player_id, $name, $price, $cargo) {
	
		// This function handles the purchasing of a new ship

		addToDebugLog("buyShip(): Function Entry: Player ID: " . $player_id . ", Name: " . $name . ", Price: " . $price . ", Cargo: " . $cargo);
	
		// Update new ship name in player table
		$dml = "INSERT INTO startrade.ships (ship_name, ship_cargo, player_id) VALUES ('" . $name . "', " . $cargo . ", " . $player_id . ");";
		$result = insert($dml);
		if ($result == TRUE) {
			addToDebugLog("buyShip(): Ship added");
		} else {
			addToDebugLog("buyShip(): Ship not added");
		}				
		
		// Update new funds in player table
		$dml = "UPDATE startrade.player SET current_funds = current_funds - " . $price . " WHERE player_id = " . $player_id . ";";
		$result = insert($dml);
		if ($result == TRUE) {
			addToDebugLog("buyShip(): Current funds updated");
		} else {
			addToDebugLog("buyShip(): Current funds not updated");
		}	
	
	}
	
	function sellShip($player_id, $id, $price) {
	
		// This function handles the purchasing of a new ship
		addToDebugLog("sellShip(): Function Entry: Player ID: " . $player_id . ", Name: " . $name . ", Price: " . $price);
		
		// Remove the ship from flotilla
		$dml = "DELETE FROM startrade.ships WHERE ship_id = " . $id . ";";
		$result = delete($dml);
		if ($result == TRUE) {
			addToDebugLog("sellShip(): Ship deleted");
		} else {
			addToDebugLog("sellShip(): Ship not deleted");
		}			
		
		// Add funds from sale of ship
		$dml = "UPDATE startrade.player SET current_funds = current_funds + " . $price . " WHERE player_id = " . $player_id . ";";
		$result = insert($dml);
		if ($result == TRUE) {
			addToDebugLog("sellShip(): Current funds updated");
		} else {
			addToDebugLog("sellShip(): Current funds not updated");
		}	
	
	
	}
	
	function getCurrentShipName($player_id) {
	
		// Updates the price for all commodities in all marketplaces
		
		addToDebugLog("getCurrentShipName(): Function Entry");	
		
		$sql = "SELECT ship_name FROM startrade.player WHERE player_id = " . $player_id . ";";
		addToDebugLog("updateMarketplaces(): Constructed query: " . $sql);	
		$result = search($sql);

		return $result[0][0];
	
	}
	
	function currentCargo($player_id) {
	
		// Calculates current cargo level for player
		
		addToDebugLog("getCurrentShipName(): Function Entry, Player ID: " . $player_id);	
		
		$sql = "SELECT commodity_id, amount FROM startrade.cargohold WHERE player_id = " . $player_id . ";";
		addToDebugLog("updateMarketplaces(): Constructed query: " . $sql);	
		$result = search($sql);
		$commodities = count($result);
		
		$total_cargo_volume = 0;
		
		for ($c = 0; $c < $commodities; $c++) {
		
			$amount = $result[$c][1];
			$cargo_unit_volume = getCommodityDetail($result[$c][0], 'size');
			$commodity_volume = $amount*$cargo_unit_volume;
			$total_cargo_volume = $total_cargo_volume + $commodity_volume;
		
		}
	
		return $total_cargo_volume;
		
	}
	
	function playerShips($player_id) {
	
		// Calculates how many ships a player has
		
		addToDebugLog("getCurrentShipName(): Function Entry, Player ID: " . $player_id);	
		
		$sql = "SELECT count(*) FROM startrade.ships WHERE player_id = " . $player_id . ";";
		addToDebugLog("playerShips(): Constructed query: " . $sql);	
		$result = search($sql);	
		addToDebugLog("playerShips(): Player " . $player_id . " has " . $result[0] . " ships");	
		
		return $result[0][0];
		
	}
	
?>