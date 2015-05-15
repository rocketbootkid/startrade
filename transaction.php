<html>

<head>
	<title>Transaction Completed</title>
</head>

<body>

<?php

	include 'functions/debug_functions.php';
	include 'functions/mysql_functions.php';
	include 'functions/startrade_functions.php';
	include 'functions/transaction_functions.php';
	
	global $current_player;
	$current_player = $_GET['player_id'];
	
	// If a transaction is detected, call the function to complete it
	if ($_GET['txn']) {
		$commodity_id = $_GET['commodity_id'];
		$marketplace_id = $_GET['marketplace_id'];
		$units = $_GET['units'];
		$unit_cost = $_GET['unit_cost'];
		$planet_id = $_GET['planet_id'];
		if ($_GET['txn'] == "buy") {
			purchase($current_player, $planet_id, $marketplace_id, $units, $unit_cost, $commodity_id);
		} elseif ($_GET['txn'] == "sell") {
			sell($planet_id, $current_player, $units, $commodity_id);
		}
	}

	//outputDebugLog();
	
	// Bit of javascript that quickly closes the tab once the work has been done
	echo "<script>window.close();</script>";
?>

</body>

</html>