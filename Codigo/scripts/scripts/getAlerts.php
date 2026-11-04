<?php
	$db = "pisid"; //database name
	//$username = $_POST["username"];
	//$password = $_POST["password"];
	$username = "root";
	$password = "abc";
	$dbhost = "localhost"; 

    $conn = mysqli_connect($dbhost,$username,$password,$db);
	$sql = "SELECT Mensagem, Leitura, Sala, Sensor, TipoAlerta, Hora, HoraEscrita from alerta where IDExperiencia IN ( SELECT IDExperiencia FROM experiencia WHERE estado = 'ADecorrer' ) AND Hora >= now() - interval 60 minute ORDER BY Hora ASC";
	$response["alerts"] = array();
	$result = mysqli_query($conn, $sql);	
	if ($result){
		if (mysqli_num_rows($result)>0){		
			while($r=mysqli_fetch_assoc($result)){	
				try {	
					$ad = array();
					$ad["Mensagem"] = $r['Mensagem'];				
					$ad["Leitura"] = $r['Leitura'];
					$ad["Sala"] = $r['Sala'];
					$ad["Sensor"] = $r['Sensor'];				
					$ad["TipoAlerta"] = $r['TipoAlerta'];
					$ad["Hora"] = $r['Hora'];
					$ad["HoraEscrita"] = $r['HoraEscrita'];
				array_push($response["alerts"], $ad);			
				}
				catch (Exception $e) {echo ($e);}
			}
		}
	}
	header('Content-Type: application/json');
	// tell browser that its a json data
	echo json_encode($response);
	//converting array to JSON string
?>