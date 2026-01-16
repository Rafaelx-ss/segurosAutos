<?php
// required headers

require '../libs/apitools.php';
require_once '../modelos/ModeloApiUsuarioAcceso.php';


$database = new Database('db');
$db = $database->getConnection();
$user = new UsuarioAcceso($database);


$parametros = file_get_contents("php://input");
$data = json_decode($parametros);
//print_r($data);
$usuario = isset($data->UserName) ? $data->UserName : '';
$macAddress = isset($data->MacAddress) ? $data->MacAddress : $macAddress;
$password = isset($data->Password) ? $data->Password : '';
$existeUsuario = $user->ObtenerDatos($usuario);


if($existeUsuario ){
	if(password_verify($password, $user->Dataset["passw"])){
		echo json_encode(array(
			"resultado" => true,
			"mensaje" => "Usuario y contraseña correctas",
			"Token" => array(
				"usuarioID" => $user->Dataset["usuarioID"],
				"accionID" => $user->Dataset["accionID"],
				"nombreAccion" => $user->Dataset["nombreAccion"],
				"formularioID" => $user->Dataset["formularioID"],
				"nombreFormulario" => $user->Dataset["nombreFormulario"]
			)
		));
	}else{
		echo json_encode(array(
			"resultado" => false,
			"mensaje" => "Contraseña incorrecta",
			"Token" => ""
		));
	}
}else{
	 echo json_encode(array(
        "resultado" => false,
        "mensaje" => "usuario no encontrado o no esta activo",
        "Token" => ""
    ));
}

?>