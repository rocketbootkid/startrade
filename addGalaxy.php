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
	
	$new_galaxy_id = generateGalaxy();
	
	ListPlanets("galaxylist", "", $_GET['player_id'], $new_galaxy_id);
	
	//outputDebugLog();

?>

<p><a href="startrade.php">Back</a>

</body>

</html>