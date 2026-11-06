<?php
    $db = getenv("DB_NAME") ?: "pisid"; 
	$dbhost = getenv("DB_HOST") ?: "localhost"; 
	$username = $_POST["username"];
	$password = $_POST["password"];
	$username = getenv("DB_USER") ?: "root";
	$password = getenv("DB_PASSWORD") ?: "abc";	
	$conn = mysqli_connect($dbhost, $username, $password, $db);	
	$sql = "SELECT SalaID, NumeroRatosSala FROM medicoessalas WHERE IDExperiencia IN ( SELECT IDExperiencia FROM experiencia WHERE estado = 'ADecorrer' ) ORDER BY SalaID ASC;";

	$result = mysqli_query($conn, $sql);
	$response["readings"] = array();
	if ($result){
		if (mysqli_num_rows($result)>0){
			while($r=mysqli_fetch_assoc($result)){
				$ad = array();
				// Alterar nome dos campos se necessario
				$ad["SalaID"] = $r['SalaID'];
				$ad["NumeroRatosSala"] = $r['NumeroRatosSala'];
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