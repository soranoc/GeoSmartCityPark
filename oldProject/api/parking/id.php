<?php
	if (count($parameters) != 3)
	   terminate("wrong number of arguments");

	$id = $parameters[2];
	$db = connect();
	$data = getData($db, $id);

	if (isset($data)) {
	   echo json_encode($data);
	} else {
	   terminate("no result(s) found");
	}

	/**
	* Fetches all the informations concerning a segment
	* and returns them as an associative array
	*
	* @param string $db the pdo database object
	* @param int $id the id of the segment
	*
	* @return array associative array containing the informations
	*/
	function getData($db, $id){
		$query = "SELECT GPSLatitude, GPSLongitude, s.Name, TotalSlotQty, OptionType, p.City, OpenFrom, CloseAt, 
					 ena.StreetName, ena.StreetNumber, pt.OptionType, exa.StreetName, wa.StreetName
					 FROM segment AS s 
					  LEFT JOIN payment_type as pt
					  ON s.Segment = pt.Segment 
					  LEFT JOIN time_open as t
					  ON s.Segment = t.Segment
					  LEFT JOIN entrance_address AS ena
					  ON s.Segment = ena.Segment
					  LEFT JOIN exit_address AS exa
					  ON s.Segment = exa.Segment
					  LEFT JOIN walking_address AS wa
					  ON s.Segment = wa.Segment
					  LEFT JOIN park_lot AS p
					  ON s.Name = p.Name 
				 WHERE s.Segment = :id;";
				 
		$sql = $db->prepare($query);
		$sql->bindParam(":id", $id, PDO::PARAM_INT);
		$sql->execute();
		
		$data = $sql->fetch();

		$lat = $data[0];
		$long = $data[1];
		$name = $data[2];
		$slots = $data[3];
		$payment = $data[4];
		$city = $data[5];
		$openingHour = $data[6];
		$closingHour = $data[7];
		$entrance = $data[8] . " " . $data[9];
		$exit = $data[11];
		$walking = $data[12];
		$payment = $data[10];

		$features = getFeatures($db, $id);
		if($slots == -1)
			  $slots = "Unknown";

		$json = array('latitude' => $lat, 'longitude' => $long, 'name' => $name, 'slots' => $slots, 'city' => $city,
	      	     'openingHour' => $openingHour, 'closingHour' => $closingHour, 'entrance' => $entrance, 'features' => $features,
		     'payment' => $payment, 'exit' => $exit, 'walking' => $walking);
		     
		return $json;
	}

	/**
	* Fetches the segment features
	*
	* @param string $db the pdo database object
	* @param int $id the id of the segment
	*
	* @return array an array of feature objects (type, desc)
	*/
	function getFeatures($db, $id) {
		$query = "SELECT i.Type, description FROM is_implemented AS i 
		       LEFT JOIN options AS o 
		       ON i.Type = o.Type
		       WHERE i.Segment = :id;";
		
		$sql = $db->prepare($query);
		$sql->bindParam(":id", $id, PDO::PARAM_INT);
		$sql->execute();

		while($data = $sql->fetch()) {
			    $features[] =  array('type' => $data[0], 'desc' => $data[1]);
		}
		
		if (!isset($features)) {
		   	    $features[] = array('type' => 'Nothing', 'desc' => '');
		}
		
		return $features;
	}
?>