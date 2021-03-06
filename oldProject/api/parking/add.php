<?php
	// maybe allow some more leeway?
	$mandatoryParameters = array('name', 'slots', 'openedFrom', 'closedAt', 'entrance', 'exit',
			     'walking', 'payment', 'latitude', 'longitude', 'features');

	// checking that no parameter is missing
	for ($i = 0; $i < count($mandatoryParameters); $i++) {
	    if (!isset($_POST[$mandatoryParameters[$i]])) {
	       terminate("missing parameter " . $mandatoryParameters[$i]);
	    }
	}

	$name = $_POST['name'];
	$slots = $_POST['slots'];
	$openingHour = $_POST['openedFrom'];
	$closingHour = $_POST['closedAt'];
	$entrance = $_POST['entrance'];
	$exit = $_POST['exit'];
	$walking = $_POST['walking'];
	$payment = $_POST['payment'];
	$lat = $_POST['latitude'];
	$lng = $_POST['longitude'];
	$features = $_POST['features'];
	
	$tmp = explode(',', $entrance);
	$city = trim($tmp[sizeof($tmp)-1], " ");

	// default values
	if (empty($openingHour))
		$openingHour = '00:00:00';
	if (empty($closingHour))
		$closingHour = '23:59:59';
	if (empty($slots))
		$slots = -1;
		
	$db = connect();
	try {
		// using a transaction to preserve the database integrity
		$db->beginTransaction();
	
		insertSegment($db, $name, $slots, $lng, $lat, $city);

		// the key of the newly added segment is needed as a foreign key for the following queries
		$id= $db->lastInsertId();

		// we need to create a default name if the name wasn't filled in the form
		if (empty($name)) {
		   $name = 'Parking #' . $id;
		   updateName($db, $id, $name);
		}
	
		insertEntranceAdress($db, $entrance, $id);
		insertWalkingAdress($db, $walking, $id);
		insertExitAdress($db, $exit, $id);
		insertOpeningHours($db, $openingHour, $closingHour, $id);
		insertPaymentType($db, $payment, $id);
		insertParkLot($db, $name, $city);
		insertFeatures($db, $id, $features);
		
		$db->commit();

	} catch(PDOExecption $e) {
	  	$db->rollBack();
          	print "Error!: " . $e->getMessage() . "</br>";
  	} 

	/**
	* Inserts a new segment with specified informations in the database
	*
	* @param string $db the pdo database object
	* @param string $name the name of the segment
	* @param int $slots the capacity of the segment
	* @param string $lat the latitude of the segment
	* @param string $lng the longitude of the segment
	* @param string $city the city of the segment
	*/
	function insertSegment($db, $name, $slots, $lng, $lat, $city){
		$query = "INSERT INTO segment 
					 (SegmentName, TotalSlotQty, Name, GPSLongitude, GPSLatitude, City)
				 VALUES (:name, :slots, :name, :lng, :lat, :city);";
				 
		$sql = $db->prepare($query);
		
		$sql->bindParam(":name", $name);
		$sql->bindParam(":slots", $slots);
		$sql->bindParam(":lng", $lng);
		$sql->bindParam(":lat", $lat);
		$sql->bindParam(":city", $city);
		
		$sql->execute();
	}
	
	/**
	* Saves the segment's entrance address in the database
	*
	* @param string $db the pdo database object
	* @param string $entrance the entrance address
	* @param int $id id of the segment
	*/
	function insertEntranceAdress($db, $entrance, $id){
		$query = "INSERT INTO entrance_address (Segment, StreetName)
				 VALUES (:id, :entrance);";
				 
		$sql = $db->prepare($query);

		$sql->bindParam(":entrance", $entrance);
		$sql->bindParam(":id", $id);

		$sql->execute();
	}
	
	/**
	* Saves the segment's exit address in the database
	*
	* @param string $db the pdo database object
	* @param string $exit the exit address
	* @param int $id id of the segment
	*/
	function insertExitAdress($db, $exit, $id){
		$query = "INSERT INTO exit_address (Segment, StreetName)
				 VALUES (:id, :exit);";
				 
		$sql = $db->prepare($query);

		$sql->bindParam(":exit", $exit);
		$sql->bindParam(":id", $id);

		$sql->execute();
	}

	/**
	* Updates the name of the Segment, used when a default name had to be computed after the insertion
	*
	* @param string $db the pdo database object	
	* @param int $id id of the segment
	* @param string $name the new name of the segment
	*/
	function updateName($db, $id, $name){
		$query = "UPDATE segment
		       	 	 SET SegmentName = :name, Name = :name
				 WHERE Segment = :id";
				 
		$sql = $db->prepare($query);

		$sql->bindParam(":name", $name);
		$sql->bindParam(":id", $id);

		$sql->execute();
	}

	/**
	* Saves the segment's walking address in the database
	*
	* @param string $db the pdo database object
	* @param string $walking the walking address
	* @param int $id id of the segment
	*/
	function insertWalkingAdress($db, $walking, $id){
		$query = "INSERT INTO walking_address (Segment, StreetName)
				 VALUES (:id, :walking);";
				 
		$sql = $db->prepare($query);

		$sql->bindParam(":walking", $walking);
		$sql->bindParam(":id", $id);

		$sql->execute();	
	}
	
	/**
	* Saves the segment's opening hours to the database
	*
	* @param string $db the pdo database object
	* @param string $openingHour the opening hour
	* @param string $closingHour the closing hour
	* @param int $id the segment id
	*/
	function insertOpeningHours($db, $openingHour, $closingHour, $id){
		$query = "INSERT INTO time_open (Segment, OpenFrom, CloseAt)
				 VALUES (:id, :opening, :closing);";

		$sql = $db->prepare($query);

		$sql->bindParam(":opening", $openingHour);
		$sql->bindParam(":closing", $closingHour);
		$sql->bindParam(":id", $id);

		$sql->execute();
	}
	
	/**
	* Save the segment's park lot in the database
	*
	* @param string $db the pdo database object
	* @param string $name the name of the park lot
	* @param string $city the city of the park lot
	*/
	function insertParkLot($db, $name, $city) {
		$query = "INSERT INTO park_lot (Name, City)
				 VALUES (:name, :city);";
				 
		$sql = $db->prepare($query);

		$sql->bindParam(":name", $name);
		$sql->bindParam(":city", $city);

		$sql->execute();
	}

	/**
	* Save the segment's payment type in the database
	*
	* @param string $db the pdo database object
	* @param string $payment the payment type
	* @param int $id the segment id
	*/
	function insertPaymentType($db, $payment, $id) {
		$query = "INSERT INTO payment_type (Segment, OptionType)
				 VALUES (:id, :payment);";
				 
		$sql = $db->prepare($query);

		$sql->bindParam(":payment", $payment);
		$sql->bindParam(":id", $id);

		$sql->execute();
	}

	/**
	* Saves the segment's features in the database
	*
	* @param string $db the pdo database object
	* @param int $id the segment id
	* @param array $features the array containing the segment's features
	*/
	function insertFeatures($db, $id, $features) {
		 for ($i = 0; $i < count($features); $i++) {
		     	 $query = "INSERT INTO is_implemented (Segment, Type)
				      VALUES (:id, :type);";
				      
				 
			$sql = $db->prepare($query);

			$sql->bindParam(":type", $features[$i]);
			$sql->bindParam(":id", $id);

			$sql->execute();
		}
	}
?>