<?php

	// generateGalaxy()			Generates number of systems and calls to...
	// generateSystems()		Generates a system name and calls to ...
	// generatePlanets()		Generates random number of planets for the supplied system
	// ListPlanets()			Displays the generated planets
	// currentSystemHeader()	Get current system name from current planet name, and write out column header
	// deleteGalaxy()			Deletes all systems and planets

	function generateGalaxy() {
	
		// This generates the galaxy
		
		addToDebugLog("generateGalaxy(): Function Entry");
		
		$galaxy_name = getGalaxyName();
		addToDebugLog("generateGalaxy(): Galaxy Name: " . $galaxy_name);
		
		$sql = "SELECT galaxy_id FROM startrade.systems ORDER BY galaxy_id DESC LIMIT 1;";
		$result = search($sql);
		$galaxy_id = $result[0][0];
		$new_galaxy_id = $galaxy_id + 1;
		addToDebugLog("generateGalaxy(): New Galaxy Id: " . $new_galaxy_id);
		
		$systems = rand(4,6);
		addToDebugLog("generateGalaxy(): - Generating " . $systems . " systems");
	
		for ($s = 0; $s < $systems; $s++) {
			generateSystems($new_galaxy_id, $galaxy_name);
		}
		
		return $new_galaxy_id;
	
	}
	
	function getGalaxyName() {
	
		$syllables = rand(2, 4);
		$name = "";
		
		for ($a = 0; $a < $syllables; $a++) {

			$consonants = array("b", "c", "d", "f", "g", "h", "j", "k", "l", "m", "n", "p", "q", "r", "s", "t", "v", "w", "x", "y", "z"); 
			$consonant = $consonants[rand(0, 20)];
			
			$vowels = array("a", "e", "i", "o", "u");
			$vowel = $vowels[rand(0, 4)];
			
			$name = $name . $consonant . $vowel;

		}
		$final_name = ucfirst($name);
		addToDebugLog("- getGalaxyName(): Generated galaxy name: " . $final_name);
		
		return $final_name;
	
	}
	
	function generateSystems($galaxy_id, $galaxy_name) {
	
		// This generates the system name

		addToDebugLog("generateSystems(): Function Entry");
		
		$syllables = rand(2, 4);
		$name = "";
		
		for ($a = 0; $a < $syllables; $a++) {

			$consonants = array("b", "c", "d", "f", "g", "h", "j", "k", "l", "m", "n", "p", "q", "r", "s", "t", "v", "w", "x", "y", "z"); 
			$consonant = $consonants[rand(0, 20)];
			
			$vowels = array("a", "e", "i", "o", "u");
			$vowel = $vowels[rand(0, 4)];
			
			
			$name = $name . $consonant . $vowel;

		}
		$final_name = ucfirst($name);
		addToDebugLog("- generateSystems(): Generated system name: " . $final_name);
		
		// Store the new system
		$dml = "INSERT INTO startrade.systems (system_name, galaxy_id, galaxy_name) VALUES ('" . $final_name . "', '" . $galaxy_id . "', '" . $galaxy_name . "');";
		$result = insert($dml);
		if ($result == TRUE) {
			addToDebugLog("- generateSystems(): New system stored OK");
		}
		
		// Get the id of the new system
		$sql = "SELECT system_id FROM startrade.systems WHERE system_name = '" . $final_name . "';";
		$result = search($sql);
		$system_id = $result[0][0];
		addToDebugLog("- generateSystems(): New system ID: " . $system_id);
		
		// Populate the new system with planets
		generatePlanets($final_name, $system_id);
	
	}
	
	function generatePlanets($system_name, $system_id) {

		// Function generates a new name and returns it to the calling function.

		// This bit generates the positional modifier, indicating the planets position in the star system
		
		addToDebugLog("generateSystem(): Function Entry - supplied parameters; System Name: " . $system_name . ", System ID: " . $system_id);
		
		$number_of_planets = rand(3, 7);
		addToDebugLog("generateSystem(): Generating " . $number_of_planets . " planets");

		$system_positions = array("alpha", "beta", "gamma", "delta", "epsilon", "zeta", "eta", "theta", "iota", "kappa", "lambda", "omicron", "sigma", "tau", "upsilon", "omega");
		
		for ($n = 0; $n < $number_of_planets; $n++) {
		
			// Choose position	
			$position = "";
			while ($position == "") {
				$element = rand(0, 15);
				$position = $system_positions[$element];
				unset($system_positions[$element]);
				addToDebugLog("- generateSystem(): Chosen position: " . $position);
			}
			
			// Construct final name
			$final_name = $system_name . " " . ucfirst($position);
			addToDebugLog("- generateSystem(): Final name: " . $final_name);
			
			// Check system / position not already taken
			$sql = "SELECT count(*) FROM startrade.planets WHERE planet_name = '" . $final_name . "';";
			$result = search($sql);
			if ($result[0][0] == 0) {
				$name_exists = FALSE;
				addToDebugLog("- generateSystem(): Planet does not exist");
			} else {
				addToDebugLog("- generateSystem(): <span style='background-color:red;'>Planet already exists</span>");
			}
			
			// Determine planet type
			$planet_types = array("Frontier", "Industrial", "Advanced");
			$planet_type = rand(0, 2);
			$planet_type = $planet_types[$planet_type];
			
			// Store new planet
			$dml = "INSERT INTO startrade.planets (planet_name, system_id, planet_type) VALUES ('" . $final_name . "', " . $system_id . ", '" . $planet_type . "');";
			$result = insert($dml);
			if ($result[0][0] == TRUE) {
				addToDebugLog("- generateSystem(): Planet created: " . $final_name);
			}
		
		}
	}

	function ListPlanets($mode, $planet_name, $player_id, $galaxy_id) {
	
		addToDebugLog("ListPlanets(): Function Entry - supplied parameters: Mode: " . $mode . ", Planet name: " . $planet_name . ", Player ID: " . $player_id . ", Galaxy ID: " . $galaxy_id);
	
		$sql = "SELECT system_id FROM startrade.systems WHERE galaxy_id = " . $galaxy_id . ";";
		$systems = search($sql);
		$system_count = count($systems);
			
		$sql = "SELECT planet_name, planets.system_id, planet_id, planet_type FROM startrade.planets, startrade.systems WHERE planets.system_id = systems.system_id AND systems.galaxy_id = " . $galaxy_id . " ORDER BY planet_name;";
		$planets = search($sql);
		$planet_count = count($planets);
		addToDebugLog("ListPlanets(): - " . $planet_count . " planets found in " . $system_count . " systems");
		
		// Display Galaxy Name
		if ($mode == "galaxylist") {
			$galaxy_details = galaxyDetailsByGalaxy($galaxy_id);
			echo "<h2>The " . $galaxy_details[0][1] . " galaxy</h2>";			
		}
		
		$current_planet = 0;
		echo "<p>\n\n<table>\n\t<tr><td valign=top>";
			echo "\n\t\t<table border=1 cellspacing=0 cellpadding=3>";
			for ($p = 0; $p < $planet_count; $p++) {
				
				// Set current system on first pass
				if ($p == 0) { 
					$current_planet = $planets[$p][1];
					currentSystemHeader($planets[$p][0]);
				}
						
				// Break onto a new column if current planet is in a new system
				if ($planets[$p][1] != $current_planet) {
					echo "\n\t\t</table>\n\t<td valign=top>\n\t\t<table border=1 cellspacing=0 cellpadding=3>";
					$current_planet = $planets[$p][1];
					currentSystemHeader($planets[$p][0]);
				}
				echo "\n\t\t\t<tr>\n\t\t\t\t<td>[" . $planets[$p][1] . ":" . $planets[$p][2] . "]\n\t\t\t\t<td>"; // System/Planet ID
				
				// Don't make a hyperlink if;
				//		1. the planet is the one the player is currently on
				//		2. the player doesn't have fuel to reach it
				
				$planet_too_far = planetTooFar($player_id, $planets[$p][0]);
								
				if ($mode == "exclude" && $planets[$p][0] == $planet_name) {
					echo "<span style='background-color:#6cc417;'>" . $planets[$p][0] . " [CS]</span>";
					addToDebugLog("ListPlanets(): Current system");
				} elseif ($planet_too_far == TRUE) {
					echo "<font color=#ddd>" . $planets[$p][0] . "</font>";
					addToDebugLog("ListPlanets(): System out of range");
				} else {
					echo "<a href='interplanetary.php?planet_id=" . $planets[$p][2] . "&player_id=" . $_GET['player_id'] . "'>" . $planets[$p][0] . "</a>";
					addToDebugLog("ListPlanets(): System in range");
				}
				
				echo "<td align=center><span title='" . $planets[$p][3] . "'>" . substr($planets[$p][3], 0, 1) . "</span>";
				
				echo "\n\t\t\t</tr>";

			}
			echo "\n\t\t</table>\n\t</tr>";
		echo "\n</table>";
	
		// Output planet / system count
		echo "\n<p>\n" . $planet_count . " planets in " . $system_count . " systems";
	
	}
	
	function currentSystemHeader($planet) {

		// Get current system name from current planet name, and write out column header
		$array = explode(" ", $planet);
		$current_system = $array[0];
		echo "<tr bgcolor=#82caff><td colspan=3 align=center><strong>" . $current_system . " System</strong></tr>";
	
	}
	
	function deleteGalaxy() {

		addToDebugLog("deleteGalaxy(): Function Entry");	
	
		// Delete all planets
		
		$dml = "DELETE FROM startrade.planets;";
		$result = delete($dml);
		if ($result[0][0] == TRUE) {
			addToDebugLog("- deleteGalaxy(): Planets destroyed");
		}
		
		// Delete all systems
		$dml = "DELETE FROM startrade.systems;";
		$result = delete($dml);
		if ($result[0][0] == TRUE) {
			addToDebugLog("- deleteGalaxy(): Systems destroyed");
		}	
	
	}

	function galaxyDetails($system_id) {
	
		// This function returns details of the galaxy the supplied system is in
	
		addToDebugLog("GalaxyDetails(): Function Entry - supplied parameters: System ID: " . $system_id);
	
		$sql = "SELECT galaxy_id, galaxy_name FROM startrade.systems WHERE system_id = " . $system_id . ";";
		addToDebugLog("GalaxyDetails(): Constructed SQL: " . $sql);
		$galaxy_details = search($sql);
	
		return $galaxy_details;
	
	}

	function galaxyDetailsByGalaxy($galaxy_id) {
	
		// This function returns details of the galaxy
	
		addToDebugLog("galaxyDetailsByGalaxy(): Function Entry - supplied parameters: Galaxy ID: " . $galaxy_id);
	
		$sql = "SELECT galaxy_id, galaxy_name FROM startrade.systems WHERE galaxy_id = " . $galaxy_id . " LIMIT 1;";
		addToDebugLog("galaxyDetailsByGalaxy(): Constructed SQL: " . $sql);
		$galaxy_details = search($sql);
	
		return $galaxy_details;
	
	}
	
?>