<?php

	// ********************************************************************************************************************************************
	// *************************************************** DEBUG-RELATED FUNCTIONS ****************************************************************
	// ********************************************************************************************************************************************	
	
	// addToDebugLog()			Adds new entries to the Debug Log
	// outputDebugLog()			Outputs the Debug Log
	// outputQueryCount()		Outputs the MySQL Query count
	
	function addToDebugLog($text) {
	
		global $debug_log;
		
		$timestamp = Date("Y-m-d H:m:s");
		
		$debug_log = $debug_log . "\n<br/>" . $timestamp . ": \t" . $text;
	
	}
	
	function outputDebugLog() {
	
		global $debug_log;
	
		echo "\n\n<p>\n\nDebug Log\n<hr>\n" . $debug_log;
	
	}

	function outputQueryCount() {
	
		global $queries;
	
		echo "<p>Query count: " . $queries;
	
	}
	
	
?>