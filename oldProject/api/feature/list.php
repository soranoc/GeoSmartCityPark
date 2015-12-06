<?php
	$db = connect();

	$query = "SELECT DISTINCT Type
	       	 	 FROM options
			 WHERE type != 'nothing'";
			 
	$sql = $db->prepare($query);;

	$sql->execute();
	
	while ($data = $sql->fetch()){
	      $json[] =$data[0];
	}

	if (isset($json))
	   echo json_encode($json);
?>