<?php
    $db = getenv("DB_NAME") ?: "pisid"; 
	$dbhost = getenv("DB_HOST") ?: "localhost";
	//$username = $_POST["username"];
	//$password = $_POST["password"];	
	$username = getenv("DB_USER") ?: "root";
	$password = getenv("DB_PASSWORD") ?: "abc";
	$conn = mysqli_connect($dbhost, $username, $password, $db);		
	$sql = "SELECT Hora, Leitura from medicoestemperatura where SensorID = 2 AND Hora >= now() - interval 10 minute ORDER BY Hora DESC";	
	$result = mysqli_query($conn, $sql);
	$response["readings"] = array();
	if ($result){
		if (mysqli_num_rows($result)>0){
			while($r=mysqli_fetch_assoc($result)){
				$ad = array();
				// Alterar nome dos campos se necessario
				$ad["Hora"] = $r['Hora'];
				$ad["Leitura"] = $r['Leitura'];
				array_push($response["readings"], $ad);
			}
		}	
	}
	mysqli_close ($conn);
	
	header('Content-Type: application/json');
	// tell browser that its a json data
	echo json_encode($response);
	//converting array to JSON string
?>