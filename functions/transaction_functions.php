<?php

	function purchase($current_player, $planet_id, $marketplace_id, $units, $unit_cost, $commodity_id) {
	
		// Completes purchase transaction
		
		addToDebugLog("purchase(): Function Entry - supplied parameters: Player ID: " . $player_id . ", Planet ID: " . $planet_id . ", Marketplace ID: " . $marketplace_id . ", Units: " . $units . ", Unit cost: " . $unit_cost . ", Commodity ID: " . $commodity_id);	

		// Removes amount from planet inventory
		$dml = "UPDATE startrade.marketplace SET commodity_units = commodity_units - " . $units . " WHERE marketplace_id = " . $marketplace_id . ";";
		$result = insert($dml);
		if ($result == TRUE) {
			addToDebugLog("purchase(): Commodity units updated");
		} else {
			addToDebugLog("purchase(): Commodity units not updated");
		}		
		
		// Adds amount to player inventory
		
		// If the commodity is fuel, add it to ship, not to cargohold
		if ($commodity_id == 16) { // It's fuel
		
			// Update fuel
			addToDebugLog("purchase(): Updating fuel");
			
			$dml = "UPDATE startrade.player SET remaining_fuel = remaining_fuel + " . $units . " WHERE player_id = " . $current_player . ";";
			$result = insert($dml);
			if ($result == TRUE) {
				addToDebugLog("purchase(): Ship fuel updated");
			} else {
				addToDebugLog("purchase(): Ship fuel not updated");
			}		
			
		} else { // It's not fuel
	
			// Determine if player already has inventory of that type
			$sql = "SELECT amount FROM startrade.cargohold WHERE player_id = " . $current_player . " AND commodity_id = " . $commodity_id . ";";
			$result = search($sql);
			$amount = $result[0][0];

			if ($amount > 0) {
				// If so, update it
				addToDebugLog("purchase(): Existing cargo found; updating count");
				
				$dml = "UPDATE startrade.cargohold SET amount = amount + " . $units . " WHERE player_id = " . $current_player . " AND commodity_id = " . $commodity_id . ";";
				$result = insert($dml);
				if ($result == TRUE) {
					addToDebugLog("purchase(): Existing cargohold units updated");
				} else {
					addToDebugLog("purchase(): Existing cargohold units not updated");
				}	
			
			} else {
				// If not, add new
				addToDebugLog("purchase(): Adding new cargo type");

				$dml = "INSERT INTO startrade.cargohold (player_id, commodity_id, amount, bought_for) VALUES (" . $current_player . ", " . $commodity_id . ", " . $units . ", " . $unit_cost . ");";
				$result = insert($dml);
				if ($result == TRUE) {
					addToDebugLog("purchase(): Existing cargohold units updated");
				} else {
					addToDebugLog("purchase(): Existing cargohold units not updated");
				}	
				
			}	

		}			
		
		// Reduce remaining credits
		$cost = $unit_cost * $units;
		$dml = "UPDATE startrade.player SET current_funds = current_funds - " . $cost . " WHERE player_id = " . $current_player . ";";
		$result = insert($dml);
		if ($result == TRUE) {
			addToDebugLog("purchase(): Player funds updated");
		} else {
			addToDebugLog("purchase(): Player funds not updated");
		}
		
		// Writes transaction to the database
		$dml = "INSERT INTO startrade.transactions (player_id, planet_id, commodity_id, commodity_units, transaction_type, commodity_unit_price) VALUES (" . $current_player . ", " . $planet_id . ", " . $commodity_id . ", " . $units . ", 1, " . $unit_cost . ");";
		$result = insert($dml);
		if ($result == TRUE) {
			addToDebugLog("purchase(): Transaction recorded");
		} else {
			addToDebugLog("purchase(): Transaction not recorded");
		}
		
	}
	
	function sell($planet_id, $player_id, $units, $commodity_id) {

		// Completes sell transaction
		
		addToDebugLog("sell(): Function Entry - supplied parameters: Player ID: " . $player_id . ", Planet ID: " . $planet_id . ", Units: " . $units . ", Commodity ID: " . $commodity_id);
		
		// Remove cargo from hold
		$dml = "DELETE FROM startrade.cargohold WHERE player_id = " . $player_id . " AND commodity_id = " . $commodity_id . ";";
		addToDebugLog("sell(): Constructed query: " . $dml);
		$result = insert($dml);
		if ($result == TRUE) {
			addToDebugLog("sell(): Existing cargohold units updated");
		} else {
			addToDebugLog("sell(): Existing cargohold units not updated");
		}		
		
		// Add cargo to marketplace
		$dml = "UPDATE startrade.marketplace SET commodity_units = commodity_units + " . $units . " WHERE planet_id = " . $planet_id . " AND commodity_id = " . $commodity_id . ";";
		addToDebugLog("sell(): Constructed query: " . $dml);
		$result = insert($dml);
		if ($result == TRUE) {
			addToDebugLog("sell(): Commodity units updated");
		} else {
			addToDebugLog("sell(): Commodity units not updated");
		}
		
		// Get marketplace value
		$sql = "SELECT commodity_unit_cost FROM startrade.marketplace WHERE planet_id = " . $planet_id . " AND commodity_id = " . $commodity_id . ";";
		addToDebugLog("sell(): Constructed query: " . $sql);	
		$result = search($sql);
		$value = $result[0][0];
		$credits = $value*$units;
		
		// Update player credits
		$dml = "UPDATE startrade.player SET current_funds = current_funds + " . $credits . " WHERE player_id = " . $player_id . ";";
		addToDebugLog("sell(): Constructed query: " . $dml);
		$result = insert($dml);
		if ($result == TRUE) {
			addToDebugLog("sell(): Player credits updated");
		} else {
			addToDebugLog("sell(): Player credits not updated");
		}
		
		// Writes transaction to the database
		$dml = "INSERT INTO startrade.transactions (player_id, planet_id, commodity_id, commodity_units, transaction_type, commodity_unit_price) VALUES (" . $player_id . ", " . $planet_id . ", " . $commodity_id . ", " . $units . ", 0, " . $value . ");";
		$result = insert($dml);
		if ($result == TRUE) {
			addToDebugLog("purchase(): Transaction recorded");
		} else {
			addToDebugLog("purchase(): Transaction not recorded");
		}
	
	}

	function displayTransactions($player_id) {
		
		// Will determine what Transaction display mode to use
		
		addToDebugLog("displayTransactions(): Function Entry - supplied parameters: Player ID: " . $player_id);
		
		//displayTransactionsBasic($player_id);
		displayTransactionsAdvanced($player_id);
	}
	
	
	function displayTransactionsBasic($player_id) {
		
		// Displays player transactions
		
		addToDebugLog("displayTransactionsBasic(): Function Entry - supplied parameters: Player ID: " . $player_id);

		// Get transaction details
		$sql = "SELECT * FROM startrade.transactions WHERE player_id = " . $player_id . " ORDER BY transaction_id DESC LIMIT 10;";
		addToDebugLog("sell(): Constructed query: " . $sql);	
		$result = search($sql);
		$rows = count($result);
		
		// 0	transaction id
		// 1	player_id
		// 2	planet_id
		// 3	commodity_id
		// 4	commodity_units
		// 5	transaction_type
		// 6	commodity_unit_price
		
		echo "<p><table width=100% border=1 cellpadding=3 cellspacing=0>";
		
		if ($rows > 0) {
			for ($c = 0; $c < $rows; $c++) {
				echo "<tr><td>";
				
				echo $result[$c][0];
				
				// Bought / Sold
				if ($result[$c][5] == 0) { // Sell
					echo ": Sold ";
				} else {
					echo ": Bought ";
				}
				
				// Units
				echo $result[$c][4] . " units of ";
				
				// Commodity
				echo $commodity_name = getCommodityDetail($result[$c][3], "commodity_name");
				
				// Price Sold For
				$price = $result[$c][6] * $result[$c][4];
				echo " for " . $price . "Cr on ";
				
				// Planet
				$planet_name = getPlanetDetails($result[$c][2], "planet_name");
				echo "<a href='interplanetary.php?planet_id=" . $result[$c][2] . "&player_id=" . $player_id . "'>" . $planet_name . "</a>";
				
				echo "</tr>";
			}
		} else {
			echo "<tr><td>There are no transactions to display</tr>";
		}
		echo "</table>";
	}

	function displayTransactionsAdvanced($player_id) {
		
		// Displays player transactions (advanced mode)
		
		addToDebugLog("displayTransactionsAdvanced(): Function Entry - supplied parameters: Player ID: " . $player_id);
		
		$count = 5;

		// Get most recent transaction details
		$sql = "SELECT * FROM startrade.transactions WHERE player_id = " . $player_id . " AND transaction_type = 0 ORDER BY transaction_id DESC LIMIT " . $count . ";";
		addToDebugLog("sell(): Constructed query: " . $sql);	
		$result = search($sql);
		$rows = count($result);
		
		// 0	transaction id
		// 1	player_id
		// 2	planet_id
		// 3	commodity_id
		// 4	commodity_units
		// 5	transaction_type
		// 6	commodity_unit_price
		
		echo "<p><table width=100% border=1 cellpadding=3 cellspacing=0>";
		echo "<tr><td bgcolor=#82caff>" . $count . " most recent completed trades</tr>";

		if ($rows > 0) {
			for ($c = 0; $c < $rows; $c++) {
				
				// For each sell transaction, find the most recent buy transaction
				$sql2 = "SELECT * FROM startrade.transactions WHERE player_id = " . $player_id . " AND transaction_type = 1 AND commodity_id = " . $result[$c][3] . " AND transaction_id < " . $result[$c][0] . " ORDER BY transaction_id DESC LIMIT 1;";
				addToDebugLog("sell(): Constructed query: " . $sql2);	
				$result2 = search($sql2);
				
				// Write Bought Line
				echo "<tr><td>+ Bought ";
				
				// Units
				echo $result2[0][4] . " units of ";
				
				// Commodity
				$commodity_name = getCommodityDetail($result2[0][3], "commodity_name");
				echo "<b>" . $commodity_name . "</b>";
				
				// Unit Price
				echo " at " . $result2[0][6] . "Cr each ";
				
				// Price Bought For
				$buy_price = $result2[0][6] * $result2[0][4];
				echo " for " . $buy_price . "Cr on ";
				
				// Planet
				$planet_name = getPlanetDetails($result2[0][2], "planet_name");
				echo "<a href='interplanetary.php?planet_id=" . $result2[0][2] . "&player_id=" . $player_id . "'>" . $planet_name . "</a>";
				
				// Write Sold Line
				echo "<br/>- Sold all ";

				// Unit Price
				echo " at " . $result[$c][6] . "Cr each";
				
				// Price Sold For
				$sell_price = $result[$c][6] * $result[$c][4];
				echo " for " . $sell_price . "Cr on ";
				
				// Planet
				$planet_name = getPlanetDetails($result[$c][2], "planet_name");
				echo "<a href='interplanetary.php?planet_id=" . $result[$c][2] . "&player_id=" . $player_id . "'>" . $planet_name . "</a>";			
				
				// Write Profit Line
				$profit = $sell_price - $buy_price;
				$percent = round(($profit / $buy_price)*100, 0);
				echo "<br/>= Made " . $profit . "Cr profit (a " . $percent . "% return)</tr>";
				$profit = 0;
				$percent = 0;

			}
		}
		
		echo "</table>";
	}
		
?>