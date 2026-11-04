<?php
	$db = "pisid";
	$dbhost = "localhost"; 
	//$username = $_POST["username"];
	//$password = $_POST["password"];
	$username = "root";
	$password = "abc";
	$sql = "SELECT Hora, Leitura from medicoestemperatura where SensorID = 1 AND Hora >= now() - interval 10 minute ORDER BY Hora DESC";
	$conn = mysqli_connect($dbhost, $username, $password, $db);		
	$result = mysqli_query($conn, $sql);
	$response["readings"] = array();
	if ($result){
		if (mysqli_num_rows($result)>0){
			try {
				while($r=mysqli_fetch_assoc($result)){			
					$ad = array();			
					$ad["Hora"] = $r['Hora'];
					$ad["Leitura"] = $r['Leitura'];
					array_push($response["readings"], $ad);			
				}
			} catch (Exception $e) {echo ($e);}																		
		}	
	}
	mysqli_close ($conn);	
	header('Content-Type: application/json');
	// tell browser that its a json data
	echo json_encode($response);
	//converting array to JSON string
?>