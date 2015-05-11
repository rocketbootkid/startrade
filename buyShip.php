<?php

	include 'functions/debug_functions.php';
	include 'functions/mysql_functions.php';
	include 'functions/startrade_functions.php';
	
	$player_id = $_GET['player_id'];
	$name = $_GET['name'];
	$price = $_GET['price'];
	$cargo = $_GET['cargo'];
	
	buyShip($player_id, $name, $price, $cargo);
	
	//outputDebugLog();
	
	// Bit of javascript that quickly closes the tab once the work has been done
	echo "<script>window.close();</script>";
?>