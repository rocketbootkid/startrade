<html>

<head>
	<title>Add New Galaxy</title>
</head>

<body>

<?php

	include 'functions/debug_functions.php';
	include 'functions/mysql_functions.php';
	include 'functions/startrade_functions.php';
	include 'functions/galaxy_functions.php';
	
	generateGalaxy();
	
	ListPlanets("", "", "");
	
	outputDebugLog();

?>

<p><a href="startrade.html">Back</a>

</body>

</html>