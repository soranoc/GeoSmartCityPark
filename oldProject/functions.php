<?php 
	/**
	* Connects to the database using the host, dbname, login and password found in the DBcredentials file
	*
	* @return PDO a pdo database object
	*/
	function connect() {

		include 'dbcredentials.php';
		
		try{
			return new PDO('mysql:host=' . $SQLhost . ';dbname=' . $SQLdbname . ';charset=utf8', $SQLlogin, $SQLpasswd);
		}
		catch (Exception $e){
			die('Erreur : ' . $e->getMessage());
		}
	}
?>