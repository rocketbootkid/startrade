<html>

<head>
	<title>All Transactions</title>
</head>

<body>

<?php

	include 'functions/debug_functions.php';
	include 'functions/mysql_functions.php';
	include 'functions/startrade_functions.php';
	include 'functions/galaxy_functions.php';
	include 'functions/marketplace_functions.php';
	include 'functions/transaction_functions.php';
	
	displayTransactions("", 10);

	//outputDebugLog();
	
?>

</body>

</html>