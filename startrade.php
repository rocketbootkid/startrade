<html>

<head>
	<title>StarTrade</title>
</head>

<body>

	<a href="addGalaxy.php?player_id=1" target="_new">Generate New Galaxy</a><br/>
	<a href="deleteGalaxy.php" target="_new">Delete Galaxy</a>
	<hr>
	<?php
	
	include 'functions/debug_functions.php';
	include 'functions/mysql_functions.php';
	include 'functions/startrade_functions.php';

	displayCurrentPlanetLink(1);
	displayCurrentPlanetLink(2);

	function displayCurrentPlanetLink($player_id) {

		$player_planet = currentPlanetDetails($player_id);
		$details = explode(",", $player_planet);
		echo "<a href='planet.php?planet_id=" . $details[0] . "&player_id=" . $player_id . "' target='_new'>Player " . $player_id . ": Go to current planet:- [" . $details[2] . ":" . $details[0] . "] " . $details[1] . "</a><br/>";
	
	}
	
	?>
	

</body>

</html>