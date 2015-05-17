<html>

<head>
	<title>Upgrade Completed</title>
</head>

<body>

<?php

	include 'functions/debug_functions.php';
	include 'functions/mysql_functions.php';
	include 'functions/startrade_functions.php';
	include 'functions/transaction_functions.php';
	include 'functions/upgrade_functions.php';

	buyUpgrade($_GET['player_id'], $_GET['upgrade_name'], $_GET['new_level'], $_GET['price']);

	//outputDebugLog();
	
	// Bit of javascript that quickly closes the tab once the work has been done
	echo "<script>window.close();</script>";
?>

</body>

</html>