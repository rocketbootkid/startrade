<?php

	include 'functions/debug_functions.php';
	include 'functions/mysql_functions.php';
	include 'functions/shipyard_functions.php';
	

	$txn = $_GET['txn'];
	
	if ($txn == "buy") {
		$player_id = $_GET['player_id'];
		$name = $_GET['name'];
		$price = $_GET['price'];
		$cargo = $_GET['cargo'];
		buyShip($player_id, $name, $price, $cargo);
	} else {
		$player_id = $_GET['player_id'];
		$ship_id = $_GET['ship_id'];
		$sale_price = $_GET['sale_price'];
		sellShip($player_id, $ship_id, $sale_price);
	}
	
	//outputDebugLog();
	
	// Bit of javascript that quickly closes the tab once the work has been done
	echo "<script>window.close();</script>";
?>