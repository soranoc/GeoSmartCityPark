<?php
	include '../functions.php';	

	$parameters = explode("/", $_GET["request"]);
	$http_verb = $_SERVER['REQUEST_METHOD'];

	header('Content-type: application/json');
	processRequest($http_verb, $parameters);

	/**
	* Stops script execution and returns a 500 http error
	* with a custom message
	* 
	* @param string $str Description of the error
	*/
	function terminate($str) {
		  http_response_code(500);
		  echo 'error ' . $str;
		  exit();
	}

	/**
	* Includes the appropriate php script
	* to process the request
	*
	* @param string $http_verb the HTTP verb
	* @param array $parameters the list of parameters passed with the request
	*/
	function processRequest($http_verb, $parameters){
		if ($http_verb == "GET" && $parameters[0] == "parking" && $parameters[1] == "zone") {
			  include("parking/zone.php");
		} else if ($http_verb == "GET" && $parameters[0] == "parking" && $parameters[1] == "id") {
			   include("parking/id.php");
		} else if ($http_verb == "POST" && $parameters[0] == "parking" && $parameters[1] == "add") {
			   include("parking/add.php");
		} else if ($http_verb == "GET" && $parameters[0] == "feature" && $parameters[1] == "list") {
			   include("feature/list.php");
		} else {
			   terminate("unrecognized request");
		}
	}
?>