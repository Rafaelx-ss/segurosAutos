<?php
$datos = require(__DIR__ . '/db.php');

$dbDir = explode("mysql:host=", $datos['dsn']);
$dbDatos = explode(";dbname=", $dbDir[1]);

$dbHostn = trim($dbDatos[0]);
$dbNamen = trim($dbDatos[1]);

				

$host = $dbHostn;
$user = $datos['username'];
$password = $datos['password'];
$database_name = $dbNamen; 

$conn = new mysqli($host, $user, $password, $database_name);
// Check connection
if ($conn->connect_error) {
    $timeData = 60*10;
}else{
	$sql = $conn->query("SELECT * FROM ConfiguracionesSistema where configuracionesSistemaID='1' limit 1");
	$result = mysqli_fetch_assoc($sql);
		
	if(isset($result['configuracionesSistemaID'])){
		if(isset($result['tiempoSesion'])){
			 $timeData = $result['tiempoSesion'];
		}else{
			 $timeData = 60*10;
		}		
	}else{
		 $timeData = 60*10;
	}
}

return [
		'time' => $timeData
	];



