<?php
	include "../functions.php";
	
	checkKey();
	resetTables();

	/**
	* Checks the key validity
	* ends the script execution on failure
	*/
	function checkKey(){
		if ($_GET['key'] != "cledeschamps")
		   exit();
	}

	/**
	* Clears the tables
	*/
	function resetTables(){
		$db = connect();
		$tables = array("entrance_address", "exit_address", "extra", "is_implemented", "park_lot",
			"payment_type", "segment", "time_open", "walking_address");
			
		$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			   
		for ($i = 0; $i < count($tables); $i++) {
			$query = "DELETE FROM " . $tables[$i];
			$db->exec($query);
		}

		echo "Tables have been reset";
	}	
?>